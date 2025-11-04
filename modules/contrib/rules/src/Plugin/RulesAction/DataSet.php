<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Provides a 'Data set' action.
 *
 * @todo 'allow NULL' for both 'data' and 'value'?
 *
 * @RulesAction(
 *   id = "rules_data_set",
 *   label = @Translation("Set a data value"),
 *   category = @Translation("Data"),
 *   context_definitions = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Data"),
 *       description = @Translation("Specifies the data to be modified using a data selector, e.g. 'node.author.name'."),
 *       allow_null = TRUE,
 *       assignment_restriction = "selector"
 *     ),
 *     "value" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("The new value to set for the specified data."),
 *       default_value = NULL,
 *       required = FALSE
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_data_set",
  label: new TranslatableMarkup("Set a data value"),
  category: new TranslatableMarkup("Data"),
  context_definitions: [
    "data" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Data"),
      description: new TranslatableMarkup("Specifies the data to be modified using a data selector, e.g. 'node.author.name'."),
      allow_null: TRUE,
      assignment_restriction: "selector"
    ),
    "value" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Value"),
      description: new TranslatableMarkup("The new value to set for the specified data."),
      default_value: NULL,
      required: FALSE
    ),
  ]
)]
class DataSet extends RulesActionBase {

  /**
   * Executes the Plugin.
   *
   * @param mixed $data
   *   Original value of an element which is being updated.
   * @param mixed $value
   *   A new value which is being set to an element identified by data selector.
   */
  protected function doExecute($data, $value) {
    $typed_data = $this->getContext('data')->getContextData();
    $typed_data->setValue($value);
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    // Saving is done at the root of the typed data tree, for example on the
    // entity level.
    $typed_data = $this->getContext('data')->getContextData();
    $root = $typed_data->getRoot();
    $value = $root->getValue();
    // Only save things that are objects and have a save() method.
    if (is_object($value) && method_exists($value, 'save')) {
      return ['data'];
    }
    return [];
  }

}
