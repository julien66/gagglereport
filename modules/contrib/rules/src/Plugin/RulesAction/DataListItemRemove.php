<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Provides a 'Remove item from list' action.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @RulesAction(
 *   id = "rules_list_item_remove",
 *   label = @Translation("Remove item from list"),
 *   category = @Translation("Data"),
 *   context_definitions = {
 *     "list" = @ContextDefinition("list",
 *       label = @Translation("List"),
 *       description = @Translation("The data list from which an item is to be removed."),
 *       assignment_restriction = "selector"
 *     ),
 *     "item" = @ContextDefinition("any",
 *       label = @Translation("Item"),
 *       description = @Translation("Item to remove.")
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_list_item_remove",
  label: new TranslatableMarkup("Remove item from list"),
  category: new TranslatableMarkup("Data"),
  context_definitions: [
    "list" => new ContextDefinition(
      data_type: "list",
      label: new TranslatableMarkup("List"),
      description: new TranslatableMarkup("The data list from which an item is to be removed."),
      assignment_restriction: "selector"
    ),
    "item" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Item"),
      description: new TranslatableMarkup("Item to remove.")
    ),
  ]
)]
class DataListItemRemove extends RulesActionBase {

  /**
   * Removes an item from a list.
   *
   * @param array $list
   *   An array to remove an item from.
   * @param mixed $item
   *   An item to remove from the array.
   */
  protected function doExecute(array $list, $item) {
    foreach (array_keys($list, $item) as $key) {
      unset($list[$key]);
    }

    $this->setContextValue('list', $list);
  }

}
