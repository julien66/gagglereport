<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\TypedData\Options\ComparisonOperatorTextOptions;

/**
 * Provides a 'Text comparison' condition.
 *
 * @Condition(
 *   id = "rules_text_comparison",
 *   label = @Translation("Text comparison"),
 *   category = @Translation("Data"),
 *   context_definitions = {
 *     "text" = @ContextDefinition("string",
 *       label = @Translation("Text"),
 *       description = @Translation("Specifies the text data to evaluate."),
 *       assignment_restriction = "selector"
 *     ),
 *     "operator" = @ContextDefinition("string",
 *       label = @Translation("Operator"),
 *       description = @Translation("The comparison operator. One of 'contains', 'starts', 'ends', or 'regex'. Defaults to 'contains'."),
 *       options_provider = "\Drupal\rules\TypedData\Options\ComparisonOperatorTextOptions",
 *       assignment_restriction = "input",
 *       default_value = "contains",
 *       required = FALSE
 *     ),
 *     "match" = @ContextDefinition("string",
 *       label = @Translation("Matching text"),
 *       description = @Translation("A string (or pattern in the case of regex) to search for in the given text data.")
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_text_comparison",
  label: new TranslatableMarkup("Text comparison"),
  category: new TranslatableMarkup("Data"),
  context_definitions: [
    "text" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Text"),
      description: new TranslatableMarkup("Specifies the text data to evaluate."),
      assignment_restriction: "selector"
    ),
    "operator" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Operator"),
      description: new TranslatableMarkup("The comparison operator. One of 'contains', 'starts', 'ends', or 'regex'. Defaults to 'contains'."),
      options_provider: ComparisonOperatorTextOptions::class,
      assignment_restriction: "input",
      default_value: "contains",
      required: FALSE
    ),
    "match" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Matching text"),
      description: new TranslatableMarkup("A string (or pattern in the case of regex) to search for in the given text data.")
    ),
  ]
)]
class TextComparison extends RulesConditionBase {

  /**
   * Evaluate the text comparison.
   *
   * @param string $text
   *   The supplied text string.
   * @param string $operator
   *   Text comparison operator. One of:
   *   - contains: (default) Evaluate if $text contains $match.
   *   - starts: Evaluate if $text starts with $match.
   *   - ends: Evaluate if $text ends with $match.
   *   - regex: Evaluate if a regular expression in $match matches $text.
   *   Values that do not match one of these operators default to "contains".
   * @param string $match
   *   The string to be compared against $text.
   *
   * @return bool
   *   The evaluation of the condition.
   */
  protected function doEvaluate($text, $operator, $match) {
    $operator = $operator ? $operator : 'contains';
    switch ($operator) {
      case 'starts':
        return strpos($text, $match) === 0;

      case 'ends':
        return strrpos($text, $match) === (strlen($text) - strlen($match));

      case 'regex':
        return (bool) preg_match('/' . str_replace('/', '\\/', $match) . '/', $text);

      case 'contains':
      default:
        // Default operator "contains".
        return strpos($text, $match) !== FALSE;
    }
  }

}
