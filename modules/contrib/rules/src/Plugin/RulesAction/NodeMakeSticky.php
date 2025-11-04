<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Makes a content item sticky.
 *
 * @RulesAction(
 *   id = "rules_node_make_sticky",
 *   label = @Translation("Make selected content sticky"),
 *   category = @Translation("Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the content item to make sticky."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_node_make_sticky",
  label: new TranslatableMarkup("Make selected content sticky"),
  category: new TranslatableMarkup("Content"),
  context_definitions: [
    "node" => new ContextDefinition(
      data_type: "entity:node",
      label: new TranslatableMarkup("Node"),
      description: new TranslatableMarkup("Specifies the content item to make sticky."),
      assignment_restriction: "selector"
    ),
  ]
)]
class NodeMakeSticky extends RulesActionBase {

  /**
   * Executes the action with the given context.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node to modify.
   */
  protected function doExecute(NodeInterface $node) {
    $node->setSticky(NodeInterface::STICKY);
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
  }

}
