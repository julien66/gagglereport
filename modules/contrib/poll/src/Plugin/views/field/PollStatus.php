<?php

namespace Drupal\poll\Plugin\views\field;

use Drupal\Core\Datetime\DateFormatterInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler which displays the flag indicating whether the poll is active.
 *
 * The display includes the runtime.
 *
 * @ViewsField("poll_status")
 */
class PollStatus extends FieldPluginBase {

  /**
   * The date formatter service.
   *
   * @var \Drupal\Core\Datetime\DateFormatterInterface
   */
  protected DateFormatterInterface $dateFormatter;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->dateFormatter = $container->get('date.formatter');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    /** @var \Drupal\poll\PollInterface $entity */
    $entity = $values->_entity;

    if ($entity->isOpen() && $entity->getRuntime() !== 0) {
      $date = $this->dateFormatter->format($entity->getCreated() + $entity->getRuntime(), 'short');
      $output = t('Active (until :date)', [':date' => rtrim(strstr($date, '-', TRUE))]);
    }
    elseif ($entity->isOpen()) {
      $output = $this->t('Active');
    }
    else {
      $output = 'Inactive';
    }

    return $output;
  }

}
