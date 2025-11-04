<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides a 'Node is sticky' condition.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @Condition(
 *   id = "rules_node_is_sticky",
 *   label = @Translation("Node is sticky"),
 *   category = @Translation("Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the node for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_node_is_sticky",
  label: new TranslatableMarkup("Node is sticky"),
  category: new TranslatableMarkup("Content"),
  context_definitions: [
    "node" => new ContextDefinition(
      data_type: "entity:node",
      label: new TranslatableMarkup("Node"),
      description: new TranslatableMarkup("Specifies the node for which to evaluate the condition."),
      assignment_restriction: "selector"
    ),
  ]
)]
class NodeIsSticky extends RulesConditionBase {

  /**
   * Check if the given node is sticky.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to check.
   *
   * @return bool
   *   TRUE if the node is sticky.
   */
  protected function doEvaluate(NodeInterface $node) {
    return $node->isSticky();
  }

}
