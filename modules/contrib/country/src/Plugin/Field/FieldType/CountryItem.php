<?php

namespace Drupal\country\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\OptionsProviderInterface;

/**
 * Plugin implementation of the 'country' field type.
 *
 * @FieldType(
 *   id = "country",
 *   label = @Translation("Country"),
 *   description = @Translation("Stores the ISO-2 name of a country."),
 *   default_widget = "country_default",
 *   default_formatter = "country_default"
 * )
 */
class CountryItem extends FieldItemBase implements OptionsProviderInterface {

  const COUNTRY_ISO_MAXLENGTH = 2;

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['value'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Country'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'value' => [
          'type' => 'char',
          'length' => static::COUNTRY_ISO_MAXLENGTH,
          'not null' => FALSE,
        ],
      ],
      'indexes' => [
        'value' => ['value'],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('value')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraint_manager = \Drupal::typedDataManager()
      ->getValidationConstraintManager();
    $constraints = parent::getConstraints();

    $constraints[] = $constraint_manager->create('ComplexData', [
      'value' => [
        'Length' => [
          'max' => static::COUNTRY_ISO_MAXLENGTH,
          'maxMessage' => $this->t('%name: the country iso-2 code may not be longer than @max characters.', [
            '%name' => $this->getFieldDefinition()
              ->getLabel(),
            '@max' => static::COUNTRY_ISO_MAXLENGTH,
          ]),
        ],
      ],
    ]);

    return $constraints;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'selectable_countries' => [],
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultFieldSettings() {
    return [
      'selectable_countries' => [],
    ] + parent::defaultFieldSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function storageSettingsForm(array &$form, FormStateInterface $form_state, $has_data) {
    $element = [];
    // We need the field-level 'selectable_countries' setting, and
    // $this->getSettings() will only provide the instance-level one, so we
    // need to explicitly fetch the field.
    $settings = $this->getFieldDefinition()
      ->getFieldStorageDefinition()
      ->getSettings();
    static::defaultCountriesForm($element, $settings);
    $element['selectable_countries']['#description'] = $this->t('If no countries are selected, all of them will be available for this field.');

    return $element;
  }

  /**
   * Builds the selectable_countries element.
   *
   * @param array $element
   *   The form associative array passed by reference.
   * @param array $settings
   *   The field settings array.
   */
  protected function defaultCountriesForm(array &$element, array $settings) {
    $element['selectable_countries'] = [
      '#type' => 'select',
      '#title' => $this->t('Selectable countries'),
      '#default_value' => $settings['selectable_countries'],
      '#options' => $this->getPossibleOptions(),
      '#description' => $this->t('Select all countries you want to make available for this field.'),
      '#multiple' => TRUE,
      '#size' => 10,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function generateSampleValue(FieldDefinitionInterface $field_definition) {
    $countries = array_keys(\Drupal::service('country.field.manager')
      ->getSelectableCountries($field_definition));

    return [
      'value' => $countries[array_rand($countries)],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(?AccountInterface $account = NULL) {
    $select_options = \Drupal::service('country.field.manager')->getList();
    return $select_options;
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(?AccountInterface $account = NULL) {
    $options = $this->getPossibleOptions($account);
    return array_keys($options);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(?AccountInterface $account = NULL) {
    $settings = $this->getFieldDefinition()
      ->getFieldStorageDefinition()
      ->getSettings();
    $selectable = array_keys($settings['selectable_countries']);
    $countries = \Drupal::service('country.field.manager')->getList();

    if (!empty($selectable)) {
      $countries = array_filter($countries,
        function ($key) use ($selectable) {
          return in_array($key, $selectable);
        },
        ARRAY_FILTER_USE_KEY
      );
    }

    return $countries;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(?AccountInterface $account = NULL) {
    $options = $this->getSettableOptions($account);
    return array_keys($options);
  }

}
