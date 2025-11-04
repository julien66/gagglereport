<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\TypedData\Options\ComparisonOperatorOptions;

/**
 * Provides a 'Data comparison' condition.
 *
 * @todo Add access callback information from Drupal 7.
 * @todo Find a way to port rules_condition_data_is_operator_options() from Drupal 7.
 *
 * @Condition(
 *   id = "rules_data_comparison",
 *   label = @Translation("Data comparison"),
 *   category = @Translation("Data"),
 *   context_definitions = {
 *     "data" = @ContextDefinition("any",
 *       label = @Translation("Data to compare"),
 *       description = @Translation("The data to be compared, specified by using a data selector, e.g. 'node.uid.entity.name.value'."),
 *       assignment_restriction = "selector"
 *     ),
 *     "operation" = @ContextDefinition("string",
 *       label = @Translation("Operator"),
 *       description = @Translation("The comparison operator. Valid values are == (default), <, >, CONTAINS (for strings or arrays) and IN (for arrays or lists)."),
 *       assignment_restriction = "input",
 *       default_value = "==",
 *       options_provider = "\Drupal\rules\TypedData\Options\ComparisonOperatorOptions",
 *       required = FALSE
 *     ),
 *     "value" = @ContextDefinition("any",
 *       label = @Translation("Data value"),
 *       description = @Translation("The value to compare the data with.")
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_data_comparison",
  label: new TranslatableMarkup("Data comparison"),
  category: new TranslatableMarkup("Data"),
  context_definitions: [
    "data" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Data to compare"),
      description: new TranslatableMarkup("The data to be compared, specified by using a data selector, e.g. 'node.uid.entity.name.value'."),
      assignment_restriction: "selector"
    ),
    "operation" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Operator"),
      description: new TranslatableMarkup("The comparison operator. Valid values are == (default), <, >, CONTAINS (for strings or arrays) and IN (for arrays or lists)."),
      assignment_restriction: "input",
      default_value: "==",
      options_provider: ComparisonOperatorOptions::class,
      required: FALSE
    ),
    "value" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Data value"),
      description: new TranslatableMarkup("The value to compare the data with.")
    ),
  ]
)]
class DataComparison extends RulesConditionBase {

  /**
   * Evaluate the data comparison.
   *
   * @param mixed $data
   *   Supplied data to test.
   * @param string $operation
   *   Data comparison operation. Typically one of:
   *     - "=="
   *     - "<"
   *     - ">"
   *     - "contains" (for strings or arrays)
   *     - "IN" (for arrays or lists).
   * @param mixed $value
   *   The value to be compared against $data.
   *
   * @return bool
   *   The evaluation of the condition.
   */
  protected function doEvaluate($data, $operation, $value) {
    $operation = $operation ? strtolower($operation) : '==';
    switch ($operation) {
      case '<':
        return $data < $value;

      case '>':
        return $data > $value;

      case 'contains':
        return is_string($data) && strpos($data, $value) !== FALSE || is_array($data) && in_array($value, $data);

      case 'in':
        return is_array($value) && in_array($data, $value);

      case '==':
      default:
        // In case both values evaluate to FALSE, further differentiate between
        // NULL values and values evaluating to FALSE.
        if (!$data && !$value) {
          return (isset($data) && isset($value)) || (!isset($data) && !isset($value));
        }
        return $data == $value;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function refineContextDefinitions(array $selected_data) {
    if (isset($selected_data['data'])) {
      $this->pluginDefinition['context_definitions']['value']->setDataType($selected_data['data']->getDataType());
      if ($this->getContextValue('operation') == 'IN') {
        $this->pluginDefinition['context_definitions']['value']->setMultiple();
      }
    }
  }

}
