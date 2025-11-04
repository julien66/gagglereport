<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Options provider to list all languages enabled on the site.
 */
class LanguageOptions extends OptionsProviderBase implements ContainerInjectionInterface {

  /**
   * The language_manager service.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a LanguageOptions object.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language_manager service.
   */
  public function __construct(LanguageManagerInterface $language_manager) {
    $this->languageManager = $language_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('language_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $languages = $this->languageManager->getLanguages(LanguageInterface::STATE_CONFIGURABLE);
    $default = $this->languageManager->getDefaultLanguage()->getId();
    $options = [LanguageInterface::LANGCODE_NOT_SPECIFIED => $this->t('Not specified')];
    foreach ($languages as $langcode => $language) {
      $options[$langcode] = $language->getName() . ($langcode == $default ? ' - default' : '') . ' (' . $langcode . ')';
    }

    // Sort the result by value for ease of locating and selecting.
    asort($options);

    return $options;
  }

}
