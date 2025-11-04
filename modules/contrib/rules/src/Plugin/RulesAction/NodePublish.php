<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\node\NodeInterface;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Publishes a content item.
 *
 * @RulesAction(
 *   id = "rules_node_publish",
 *   label = @Translation("Publish a content item"),
 *   category = @Translation("Content"),
 *   context_definitions = {
 *     "node" = @ContextDefinition("entity:node",
 *       label = @Translation("Node"),
 *       description = @Translation("Specifies the content item to publish."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_node_publish",
  label: new TranslatableMarkup("Publish a content item"),
  category: new TranslatableMarkup("Content"),
  context_definitions: [
    "node" => new ContextDefinition(
      data_type: "entity:node",
      label: new TranslatableMarkup("Node"),
      description: new TranslatableMarkup("Specifies the content item to publish."),
      assignment_restriction: "selector"
    ),
  ]
)]
class NodePublish extends RulesActionBase {

  /**
   * Publishes the content.
   *
   * @param \Drupal\Core\Entity\NodeInterface $node
   *   The node to modify.
   */
  protected function doExecute(NodeInterface $node) {
    $node->setPublished();
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // The node should be auto-saved after the execution.
    return ['node'];
  }

}
