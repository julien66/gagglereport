<?php

namespace Drupal\bootstrap_flag_icons\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\language\ConfigurableLanguageManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides language switcher block plugin definitions for all languages.
 */
class BootstrapFlagIconsBlock extends DeriverBase implements ContainerDeriverInterface {

  use StringTranslationTrait;

  /**
   * Constructs a new BootstrapFlagIconsBlock object.
   *
   * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
   *   The language manager.
   */
  public function __construct(protected readonly LanguageManagerInterface $languageManager) {
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new self(
      $container->get('language_manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    if ($this->languageManager instanceof ConfigurableLanguageManagerInterface) {
      $info = $this->languageManager->getDefinedLanguageTypesInfo();
      $configurable_types = $this->languageManager->getLanguageTypes();
      foreach ($configurable_types as $type) {
        $this->derivatives[$type] = $base_plugin_definition;
        $this->derivatives[$type]['admin_label'] = $this->t('Bootstrap Language switcher (@type)', ['@type' => $info[$type]['name']]);
      }
      // If there is just one configurable type then change the title of the
      // block.
      if (count($configurable_types) == 1) {
        $this->derivatives[reset($configurable_types)]['admin_label'] = $this->t('Bootstrap Language switcher');
      }
    }

    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
