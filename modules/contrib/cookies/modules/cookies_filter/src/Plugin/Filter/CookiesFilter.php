<?php

namespace Drupal\cookies_filter\Plugin\Filter;

use Drupal\cookies\Services\CookiesConfigService;
use Drupal\cookies_filter\Services\CookiesFilterElementTypesService;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\Plugin\FilterBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a filter to apply cookies two-click blocker.
 *
 * Provides a filter to apply cookies two-click blocker on certain elements with
 * external content like iframes, embed, object, link, img.
 *
 * @Filter(
 *   id = "cookies_filter",
 *   title = @Translation("COOKiES Filter: 2-Click Consent for page elements"),
 *   description = @Translation("Prevents loading for selected HTML elements
 *    with external content like iframes, embed, object, link, img and shows a
 *    placeholder consent banner to accept the according cookie grups."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
 * )
 */
class CookiesFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * The famous Drupal Language Manager.
   *
   * @var \Drupal\cookies\Services\CookiesConfigService
   */
  protected $cookiesConfigService;

  /**
   * The cookies filter element types service.
   *
   * @var \Drupal\cookies_filter\Services\CookiesFilterElementTypesService
   */
  protected $cookiesFilterElementTypesService;

  /**
   * Constructs a CookiesFilter object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\cookies\Services\CookiesConfigService $cookies_config_service
   *   The cookies config service.
   * @param \Drupal\cookies_filter\Services\CookiesFilterElementTypesService $cookiesFilterElementTypesService
   *   The cookies filter element type service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CookiesConfigService $cookies_config_service, CookiesFilterElementTypesService $cookiesFilterElementTypesService) {
    $this->cookiesConfigService = $cookies_config_service;
    $this->cookiesFilterElementTypesService = $cookiesFilterElementTypesService;
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('cookies.config'),
      $container->get('cookies_filter.element_types')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    return $this->t('Use COOKiES Service Filters to block loading for
    selected HTML elements like iframes, embed, object, link, img, script, ...
    until consent is given and show an optional placeholder instead.');
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    try {
      return $this->cookiesFilterElementTypesService->filterText($text, $langcode);
    }
    catch (\Exception $th) {
      throw $th;
    }
  }

}
