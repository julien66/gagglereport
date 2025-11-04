<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides a 'List contains' condition.
 *
 * @todo Add access callback information from Drupal 7?
 * @todo Add group information from Drupal 7?
 * @todo Add info alter
 *
 * @Condition(
 *   id = "rules_list_contains",
 *   label = @Translation("List contains item"),
 *   category = @Translation("Data"),
 *   context_definitions = {
 *     "list" = @ContextDefinition("list",
 *       label = @Translation("List"),
 *       description = @Translation("The list to be checked."),
 *       assignment_restriction = "selector"
 *     ),
 *     "item" = @ContextDefinition("any",
 *       label = @Translation("Item"),
 *       description = @Translation("The item to check for.")
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_list_contains",
  label: new TranslatableMarkup("List contains item"),
  category: new TranslatableMarkup("Data"),
  context_definitions: [
    "list" => new ContextDefinition(
      data_type: "list",
      label: new TranslatableMarkup("List"),
      description: new TranslatableMarkup("The list to be checked."),
      assignment_restriction: "selector"
    ),
    "item" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Item"),
      description: new TranslatableMarkup("Item to check for.")
    ),
  ]
)]
class DataListContains extends RulesConditionBase {

  /**
   * Evaluate whether the list has the item.
   *
   * @param array|\Drupal\Core\TypedData\ListInterface $list
   *   List to be searched.
   * @param mixed $item
   *   Item to be found in list.
   */
  protected function doEvaluate($list, $item) {
    if ($item instanceof EntityInterface && $id = $item->id()) {
      // Check for equal items using the identifier if there is one.
      foreach ($list as $list_item) {
        if ($list_item->id() == $id) {
          return TRUE;
        }
      }
      return FALSE;
    }

    return in_array($item, $list);
  }

}
