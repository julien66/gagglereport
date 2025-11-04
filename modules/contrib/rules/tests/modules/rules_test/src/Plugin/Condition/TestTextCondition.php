<?php

namespace Drupal\rules_test\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Returns TRUE if the 'text' context parameter equals 'test value'.
 *
 * @Condition(
 *   id = "rules_test_string_condition",
 *   label = @Translation("Test condition using a string"),
 *   category = @Translation("Tests"),
 *   context_definitions = {
 *     "text" = @ContextDefinition("string",
 *       label = @Translation("Text to compare")
 *     ),
 *   },
 *   configure_permissions = { "access test configuration" }
 * )
 */
#[Condition(
  id: "rules_test_string_condition",
  label: new TranslatableMarkup("Test condition using a string"),
  category: new TranslatableMarkup("Tests"),
  context_definitions: [
    "text" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Text to compare")
    ),
  ],
  configure_permissions: [
    "access test configuration",
  ]
)]
class TestTextCondition extends RulesConditionBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    $text = $this->getContextValue('text');
    return $text == 'test value';
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // We don't care about summaries for test condition plugins.
    return '';
  }

}
