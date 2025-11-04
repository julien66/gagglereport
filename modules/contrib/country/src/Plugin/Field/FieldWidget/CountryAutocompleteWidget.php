<?php

namespace Drupal\country\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\country\CountryFieldManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'country_autocomplete' widget.
 *
 * @FieldWidget(
 *   id = "country_autocomplete",
 *   label = @Translation("Country autocomplete"),
 *   field_types = {
 *     "country"
 *   }
 * )
 */
class CountryAutocompleteWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The country field manager.
   *
   * @var \Drupal\country\CountryFieldManager
   */
  protected $countryFieldManager;

  /**
   * Constructs a new CountryAutocompleteWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition configuration.
   * @param array $settings
   *   The formatter settings.
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
    array $third_party_settings,
    CountryFieldManager $country_field_manager,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);
    $this->countryFieldManager = $country_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
     $plugin_id, $plugin_definition,
     $configuration['field_definition'],
     $configuration['settings'],
     $configuration['third_party_settings'],
     $container->get('country.field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'size' => '60',
      'autocomplete_route_name' => 'country.autocomplete',
      'placeholder' => t('Start typing a country name ...'),
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $countries = $this->countryFieldManager->getSelectableCountries($this->fieldDefinition);
    $element['value'] = $element + [
      '#type' => 'textfield',
      '#default_value' => (isset($items[$delta]->value) && isset($countries[$items[$delta]->value])) ? $countries[$items[$delta]->value] : '',
      '#autocomplete_route_name' => $this->getSetting('autocomplete_route_name'),
      '#autocomplete_route_parameters' => [
        'entity_type' => $this->fieldDefinition->get('entity_type'),
        'bundle' => $this->fieldDefinition->get('bundle'),
        'field_name' => $this->fieldDefinition->get('field_name'),
      ],
      '#size' => $this->getSetting('size'),
      '#placeholder' => $this->getSetting('placeholder'),
      '#maxlength' => 255,
      '#selectable_countries' => $countries,
      '#element_validate' => [[$this, 'validateElement']],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $element = parent::settingsForm($form, $form_state);
    $element['size'] = [
      '#type' => 'number',
      '#title' => $this->t('Size'),
      '#default_value' => $this->getSetting('size'),
      '#required' => TRUE,
      '#min' => 20,
    ];
    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
    ];
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $summary[] = $this->t('Size: @size', ['@size' => $this->getSetting('size')]);
    $summary[] = $this->t('Placeholder: @placeholder', ['@placeholder' => $this->getSetting('placeholder')]);
    return $summary;
  }

  /**
   * Form element validate handler for country autocomplete.
   */
  public static function validateElement($element, FormStateInterface $form_state) {
    if ($country = $element['#value']) {
      $countries = $element['#selectable_countries'];
      $iso2 = array_search($country, $countries);
      if (!empty($iso2)) {
        $form_state->setValueForElement($element, $iso2);
      }
      else {
        $form_state->setError($element, t('An unexpected country has been entered.'));
      }
    }
  }

}
