<?php

namespace Drupal\country;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Locale\CountryManagerInterface;

/**
 * Defines a class for country field management.
 */
class CountryFieldManager {

  /**
   * The country manager.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * The language manager.
   *
   * @var \Drupal\Core\Language\LanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Constructs a new CountryAutocompleteController.
   *
   * @param \Drupal\Core\Locale\CountryManagerInterface $country_manager
   *   The country manager.
   * @param \Drupal\Core\Language\LanguageManagerInterface $language_manager
   *   The language manager.
   */
  public function __construct(
    CountryManagerInterface $country_manager,
    LanguageManagerInterface $language_manager,
  ) {
    $this->countryManager = $country_manager;
    $this->languageManager = $language_manager;
  }

  /**
   * Get array of selectable countries.
   *
   * If some countries have been selected at the default field settings, allow
   * only those to be selectable. Else, check if any have been selected for the
   * field instance. If none, allow all available countries.
   *
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The field definition object.
   *
   * @return array
   *   The array of country names keyed by their ISO2 values.
   */
  public function getSelectableCountries(FieldDefinitionInterface $field_definition) {
    $field_definition_countries = $field_definition->getSetting('selectable_countries');
    $field_storage_countries = $field_definition->getFieldStorageDefinition()->getSetting('selectable_countries');

    $countries = $this->getList();

    $allowed = (!empty($field_definition_countries)) ? $field_definition_countries : $field_storage_countries;
    return (!empty($allowed)) ? array_intersect_key($countries, $allowed) : $countries;
  }

  /**
   * Get array of countries.
   *
   * @return array
   *   The array of country names keyed by their ISO2 values.
   */
  public function getList() {
    $countries = $this->countryManager->getList();
    if (extension_loaded('intl')) {
      // Sorts the country array depending on language rules.
      $collator = \Collator::create($this->languageManager->getCurrentLanguage()->getId());
      $collator->asort($countries);
    }
    else {
      asort($countries);
    }
    return $countries;
  }

}
