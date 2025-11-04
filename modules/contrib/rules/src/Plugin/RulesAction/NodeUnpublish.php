<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Unpublishes a content item.
 *
 * @RulesAction(
 *   id = "rules_node_unpublish",
 *   label = @Translation("Unpublish a content item"),
 *   category = @Translation("Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the content item to unpublish."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_node_unpublish",
  label: new TranslatableMarkup("Unpublish a content item"),
  category: new TranslatableMarkup("Content"),
  context_definitions: [
    "node" => new ContextDefinition(
      data_type: "entity:node",
      label: new TranslatableMarkup("Node"),
      description: new TranslatableMarkup("Specifies the content item to unpublish."),
      assignment_restriction: "selector"
    ),
  ]
)]
class NodeUnpublish extends RulesActionBase {

  /**
   * Unpublishes the Node.
   *
   * @param \Drupal\Core\Entity\NodeInterface $node
   *   The node to modify.
   */
  protected function doExecute(NodeInterface $node) {
    $node->setUnpublished();
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
  }

}
