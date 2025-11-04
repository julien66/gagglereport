<?php

namespace Drupal\country\Plugin\views\sort;

use Drupal\Core\Database\Database;
use Drupal\Core\Form\FormStateInterface;
use Drupal\country\CountryFieldManager;
use Drupal\views\Plugin\views\sort\SortPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Sort handler for country fields.
 *
 * @ingroup views_sort_handlers
 *
 * @ViewsSort("country_item")
 */
class CountryItem extends SortPluginBase {

  /**
   * The country field manager service.
   *
   * @var \Drupal\country\CountryFieldManager
   */
  private $countryFieldManager;

  /**
   * Constructs a CountryItem object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\country\CountryFieldManager $countryFieldManager
   *   The country field manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CountryFieldManager $countryFieldManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->countryFieldManager = $countryFieldManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('country.field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['default_sort'] = ['default' => 0];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
    $form['default_sort'] = [
      '#type' => 'radios',
      '#title' => $this->t('Sort by ISO code'),
      '#options' => [$this->t('No'), $this->t('Yes')],
      '#default_value' => $this->options['default_sort'],
    ];
  }

  /**
   * Called to add the sort to a query.
   *
   * Sort by index of country names using sql FIELD function.
   *
   * @see http://dev.mysql.com/doc/refman/5.5/en/string-functions.html#function_field
   */
  public function query() {
    // Fall back to default sort for sorting by country code.
    if ($this->options['default_sort']) {
      return parent::query();
    }

    $this->ensureMyTable();
    $country_codes = array_keys($this->countryFieldManager->getList());
    $connection = Database::getConnection();
    $formula = 'FIELD(' . $this->getField() . ', ' . implode(', ', array_map(
      [$connection, 'quote'], $country_codes)
    ) . ')';
    $this->query->addOrderBy(NULL, $formula, $this->options['order'], $this->tableAlias . '_' . $this->field . '_country_name_sort');
  }

}
