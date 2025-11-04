<?php

namespace Drupal\country\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\country\CountryFieldManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'country' formatter.
 *
 * @FieldFormatter(
 *   id = "country_default",
 *   module = "country",
 *   label = @Translation("Country"),
 *   field_types = {
 *     "country"
 *   }
 * )
 */
class CountryDefaultFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The country field manager.
   *
   * @var \Drupal\country\CountryFieldManager
   */
  protected $countryFieldManager;

  /**
   * Constructs a new CountryDefaultFormatter object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition configuration.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\country\CountryFieldManager $country_field_manager
   *   The country field manager.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    CountryFieldManager $country_field_manager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->countryFieldManager = $country_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('country.field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $countries = $this->countryFieldManager->getSelectableCountries($this->fieldDefinition);
    foreach ($items as $delta => $item) {
      if (isset($countries[$item->value])) {
        $elements[$delta] = ['#markup' => $countries[$item->value]];
      }
    }
    return $elements;
  }

}
