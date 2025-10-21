<?php

namespace Drupal\cookies_filter\Services;

use Drupal\cookies\Constants\CookiesConstants;
use Drupal\cookies_filter\Entity\CookiesServiceFilterEntity;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\filter\FilterProcessResult;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Provides a service class for filtering HTML elements nad other tasks.
 */
class CookiesFilterElementTypesService {
  use StringTranslationTrait;

  /**
   * The famous Drupal Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Provides a html element mapping.
   *
   * Provides the radios list for
   * Drupal\cookies_filter\Form\CookiesServiceFilterEntityForm.
   */
  public function getElementTypesSelectList() {
    return [
      'iframe' => $this->t('IFrame (@example)', ['@example' => '<iframe>...</iframe>']),
      'embed' => $this->t('Embed (@example)', ['@example' => '<embed type="..." src="...">']),
      'object' => $this->t('Object (@example)', ['@example' => '<object data="..."></object>']),
      'img' => $this->t('Image (@example)', ['@example' => '<img src="...">']),
      'script' => $this->t('Script (@example), see note!', ['@example' => '<script src="..." />']),
    ];
  }

  /**
   * Retreives all cookies service filter and filters the text.
   *
   * Retreives all cookies service filter and filters the text using
   * filterTextByServiceFilter.
   */
  public function filterText($text, $langcode) {
    // Load the defined CookieServiceFilterEntities:
    $cookiesFilterEntities = $this->entityTypeManager->getStorage('cookies_service_filter')->loadMultiple();
    foreach ($cookiesFilterEntities as $cookiesFilterEntity) {
      /**
       * @var \Drupal\cookies_filter\Entity\CookiesServiceFilterEntity $cookiesFilterEntity
       */
      $enabled = $cookiesFilterEntity->get('status');
      // Only handle enabled $cookiesFilterEntity's:
      if ($enabled) {
        $text = $this->filterTextByServiceFilter($cookiesFilterEntity, $text, $langcode);
      }
    }

    // We need to filter the result here:
    $result = new FilterProcessResult($text);
    $result->setAttachments([
      'library' => ['cookies_filter/cookies_filter'],
    ]);
    return $result;
  }

  /**
   * Filters text according to the selected CookiesServiceFilterEntity.
   */
  protected function filterTextByServiceFilter(CookiesServiceFilterEntity $cookiesFilterEntity, $text, $langcode) {
    $serviceId = $cookiesFilterEntity->get('service');
    $elementSelectors = trim($cookiesFilterEntity->get('elementSelectors'));
    $elementSelectorsArray = [];
    // Split the elementSelectors into an array:
    if (!empty($elementSelectors)) {
      $elementSelectorsArray = explode("\n", $elementSelectors);
    }
    $placeholderBehaviour = $cookiesFilterEntity->get('placeholderBehaviour');
    $placeholderCustomElementSelectors = trim($cookiesFilterEntity->get('placeholderCustomElementSelectors'));
    $placeholderCustomElementSelectorArray = [];
    if (!empty($placeholderCustomElementSelectors)) {
      $placeholderCustomElementSelectorArray = explode("\n", $placeholderCustomElementSelectors);
    }

    // @todo Add twig file and suggestions for the placeholder content and
    // make content translatable
    $dom_element_to_handle = $cookiesFilterEntity->get('elementType');
    $crawler = new Crawler($text);
    if (!empty($dom_element_to_handle)) {
      if (empty($elementSelectorsArray)) {
        // No elementSelector entered by the user.
        $filteredCrawler = $crawler->filter($dom_element_to_handle);
      }
      else {
        // Join $elementSelectors with "," (or):
        $elementSelectorsString = trim(implode(',', $elementSelectorsArray));
        $filteredCrawler = $crawler->filter($elementSelectorsString);
      }
      if ($filteredCrawler->count() <= 0) {
        return $text;
      }
      foreach ($filteredCrawler as $domElement) {
        /**
         * @var \DOMElement $domElement
         */
        if (mb_strtolower($domElement->tagName) != $dom_element_to_handle) {
          throw new \Exception('Returned elements must match the selected element type: ' . $dom_element_to_handle);
        }
        switch ($dom_element_to_handle) {

          case 'iframe':
          case 'embed':
          case 'img':
            if (!$domElement->hasAttribute('src')) {
              // Skip elements early that have no src.
              continue 2;
            }

            $src = $domElement->getAttribute('src');

            if (empty($src)) {
              // Skip on empty source:
              continue 2;
            }

            $domElement->setAttribute('data-src', $src);
            $domElement->setAttribute('data-cookieconsent', $serviceId);
            $domElement->setAttribute('class', 'cookies-filter-processed cookies-filter-replaced--src cookies-filter-service--' . $serviceId . ' ' . $domElement->getAttribute('class'));
            $domElement->removeAttribute('src');
            break;

          case 'object':

            if (!$domElement->hasAttribute('data')) {
              // Skip elements early that have no data.
              continue 2;
            }

            $data = $domElement->getAttribute('data');

            if (empty($data)) {
              // Skip on empty data:
              continue 2;
            }

            $domElement->setAttribute('data-data', $data);
            $domElement->setAttribute('data-cookieconsent', $serviceId);
            $domElement->setAttribute('class', 'cookies-filter-processed cookies-filter-replaced--data cookies-filter-service--' . $serviceId . ' ' . $domElement->getAttribute('class'));
            $domElement->removeAttribute('data');
            break;

          case 'script':
            // If $domElement is an remote script, also remove src:
            if ($domElement->hasAttribute('src')) {
              $domElement->setAttribute('data-src', $domElement->getAttribute('src'));
              $domElement->removeAttribute('src');
              $domElement->setAttribute('class', 'cookies-filter-replaced--src ' . $domElement->getAttribute('class'));
            }
            $domElement->setAttribute('type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
            $domElement->setAttribute('data-cookieconsent', $serviceId);
            $domElement->setAttribute('class', 'cookies-filter-processed cookies-filter-replaced--type cookies-filter-service--' . $serviceId . ' ' . $domElement->getAttribute('class'));
            break;

          default:
            throw new \Exception('The dom element "' . $dom_element_to_handle . '" is not supported');
        }
        if (empty($placeholderCustomElementSelectorArray)) {
          if ($placeholderBehaviour == 'overlay') {
            $domElement->setAttribute('class', 'cookies-filter-placeholder-type-overlay ' . $domElement->getAttribute('class'));
          }
          elseif ($placeholderBehaviour == 'hide') {
            $domElement->setAttribute('class', 'cookies-filter-placeholder-type-hidden ' . $domElement->getAttribute('class'));
          }
        }
      }
      if (!empty($placeholderCustomElementSelectorArray) && $placeholderBehaviour != 'none') {
        $placeholderCustomElementSelectorsString = trim(implode(',', $placeholderCustomElementSelectorArray));
        // Filter unlegit html selectors:
        $filteredCustomElementSelectorCrawler = $crawler->filter($placeholderCustomElementSelectorsString);
        foreach ($filteredCustomElementSelectorCrawler as $customDomElement) {
          if ($placeholderBehaviour == 'overlay') {
            $customDomElement->setAttribute('class', 'cookies-filter-custom cookies-filter-placeholder-type-overlay cookies-filter-service--' . $serviceId . ' ' . $customDomElement->getAttribute('class'));
          }
          elseif ($placeholderBehaviour == 'hide') {
            $customDomElement->setAttribute('class', 'cookies-filter-custom cookies-filter-placeholder-type-hidden cookies-filter-service--' . $serviceId . ' ' . $customDomElement->getAttribute('class'));
          }
        }
      }
    }
    // Scripts always go in head, so we need to return both body and head:
    return $crawler->html();
  }

}
