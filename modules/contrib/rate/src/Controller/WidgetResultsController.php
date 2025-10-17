<?php

namespace Drupal\rate\Controller;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\node\NodeInterface;
use Drupal\rate\Plugin\RateWidgetBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Rate routes.
 */
class WidgetResultsController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The rate widget base service.
   *
   * @var \Drupal\rate\Plugin\RateWidgetBase
   */
  protected $rateWidgetBase;

  /**
   * Constructs an EntityUntranslatableFieldsConstraintValidator object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param Drupal\rate\Plugin\RateWidgetBase $rate_widget_base
   *   The rate widget base service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, RateWidgetBase $rate_widget_base) {
    $this->entityTypeManager = $entity_type_manager;
    $this->rateWidgetBase = $rate_widget_base;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('rate.vote_widget_base')
    );
  }

  /**
   * Display rate voting results views on nodes.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node for which to display results.
   *
   * @return array
   *   The render array.
   */
  public function nodeResults(NodeInterface $node) {
    $entity_type_id = $node->getEntityTypeId();
    $bundle = $node->bundle();
    // First, make sure the data is fresh.
    $cache_bins = Cache::getBins();
    $cache_bins['data']->deleteAll();

    // Check if the node has widgets enabled.
    $widgets = $this->entityTypeManager->getStorage('rate_widget')->loadByProperties(['entity_types.*' => $entity_type_id . '.' . $bundle]);

    if (empty($widgets)) {
      throw new NotFoundHttpException();
    }

    $page = [];
    foreach ($widgets as $widget => $widget_variables) {
      // Get and return the rate results views.
      $page[] = [
        '#type' => '#markup',
        '#markup' => '<strong>' . $widget_variables->label() . '</strong>',
      ];

      // Show the results per voting option for this node.
      $header = [
        ['data' => $this->t('Option')],
        ['data' => $this->t('Count'), 'class' => 'views-align-right'],
        ['data' => $this->t('Sum'), 'class' => 'views-align-right'],
      ];
      $rows = [];
      $options = $widget_variables->get('options');

      // Get the aggregated results grouped by option.
      $aggregation = 'COUNT';
      $values_count = $this->rateWidgetBase->getVotes($node->getEntityTypeId(), $node->bundle(), $node->id(), 'updown', $widget_variables->get('value_type'), $widget_variables->id(), NULL, $aggregation);
      $aggregation = 'SUM';
      $values_sum = $this->rateWidgetBase->getVotes($node->getEntityTypeId(), $node->bundle(), $node->id(), 'updown', $widget_variables->get('value_type'), $widget_variables->id(), NULL, $aggregation);

      // Build up the rows and add class to the cells.
      foreach ($options as $id => $option) {
        $count = $values_count[$option['value']] ?? 0;
        $sum = isset($values_sum[$option['value']]) ? number_format($values_sum[$option['value']], 2) : '0.00';
        $rows[] = [
          ['data' => $option['label']],
          ['data' => $count, 'class' => 'views-align-right'],
          ['data' => $sum, 'class' => 'views-align-right'],
        ];
      }
      $page[] = [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
      ];
      $page[] = views_embed_view('rate_widgets_results', 'node_results_block', $node->id(), $node->getEntityTypeId(), $widget);
    }

    return $page;
  }

}
