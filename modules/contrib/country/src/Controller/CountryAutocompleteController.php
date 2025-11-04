<?php

namespace Drupal\country\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\country\CountryFieldManager;
use Drupal\field\Entity\FieldConfig;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Returns autocomplete responses for countries.
 */
class CountryAutocompleteController implements ContainerInjectionInterface {

  /**
   * The country field manager.
   *
   * @var \Drupal\country\CountryFieldManager
   */
  protected $countryFieldManager;

  /**
   * Constructs a new CountryAutocompleteController.
   *
   * @param \Drupal\country\CountryFieldManager $country_field_manager
   *   The country field manager.
   */
  public function __construct(CountryFieldManager $country_field_manager) {
    $this->countryFieldManager = $country_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('country.field.manager')
    );
  }

  /**
   * Returns response for the country name autocompletion.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object containing the search string.
   * @param string $entity_type
   *   The type of entity that owns the field.
   * @param string $bundle
   *   The name of the bundle that owns the field.
   * @param string $field_name
   *   The name of the field.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the autocomplete suggestions for countries.
   */
  public function autocomplete(Request $request, $entity_type, $bundle, $field_name) {
    $matches = [];
    $string = $request->query->get('q');
    if ($string) {
      // Check if the bundle is global - in that case we need all countries.
      if ($bundle == 'global') {
        $countries = $this->countryFieldManager->getList();
      }
      else {
        /** @var \Drupal\Core\Field\FieldDefinitionInterface $field_definition */
        $field_definition = FieldConfig::loadByName($entity_type, $bundle, $field_name);
        $countries = $this->countryFieldManager->getSelectableCountries($field_definition);
      }
      foreach ($countries as $country) {
        if (strpos(mb_strtolower($country), mb_strtolower($string)) !== FALSE) {
          $matches[] = ['value' => $country, 'label' => $country];
        }
      }
    }
    return new JsonResponse($matches);
  }

}
