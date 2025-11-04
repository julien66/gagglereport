<?php

namespace Drupal\country\Plugin\facets\processor;

use Drupal\Core\Locale\CountryManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\facets\FacetInterface;
use Drupal\facets\Processor\BuildProcessorInterface;
use Drupal\facets\Processor\ProcessorPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a processor that displays the country name instead of its code.
 *
 * @FacetsProcessor(
 *   id = "country_name",
 *   label = "Country name",
 *   description = @Translation("Display country name instead of its code."),
 *   stages = {
 *     "build" = 5,
 *   },
 * )
 */
class CountryName extends ProcessorPluginBase implements BuildProcessorInterface, ContainerFactoryPluginInterface {

  /**
   * Country manager service.
   *
   * @var \Drupal\Core\Locale\CountryManagerInterface
   */
  protected $countryManager;

  /**
   * Constructs a CountryName object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Locale\CountryManagerInterface $country_manager
   *   The country manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CountryManagerInterface $country_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->countryManager = $country_manager;
  }

  /**
   * Creates an instance of the plugin.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('country_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build(FacetInterface $facet, array $results) {
    foreach ($results as $result) {
      $code = $result->getRawValue();
      $name = $this->countryManager->getList()[$code] ?? $code;
      $result->setDisplayValue($name);
    }

    return $results;
  }

}
