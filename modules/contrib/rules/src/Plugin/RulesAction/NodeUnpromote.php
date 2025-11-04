<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Demotes a content item.
 *
 * @RulesAction(
 *   id = "rules_node_unpromote",
 *   label = @Translation("Demote selected content from front page"),
 *   category = @Translation("Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the content item to unpromote."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_node_unpromote",
  label: new TranslatableMarkup("Demote selected content from front page"),
  category: new TranslatableMarkup("Content"),
  context_definitions: [
    "node" => new ContextDefinition(
      data_type: "entity:node",
      label: new TranslatableMarkup("Node"),
      description: new TranslatableMarkup("Specifies the content item to unpromote."),
      assignment_restriction: "selector"
    ),
  ]
)]
class NodeUnpromote extends RulesActionBase {

  /**
   * Executes the action with the given context.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to modify.
   */
  protected function doExecute(NodeInterface $node) {
    $node->setPromoted(NodeInterface::NOT_PROMOTED);
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
  }

}
