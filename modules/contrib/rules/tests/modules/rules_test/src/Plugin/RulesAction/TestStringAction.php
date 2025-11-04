<?php

namespace Drupal\rules_test\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Provides a test action that concatenates a string to itself.
 *
 * @RulesAction(
 *   id = "rules_test_string",
 *   label = @Translation("Test concatenate action"),
 *   category = @Translation("Tests"),
 *   context_definitions = {
 *     "text" = @ContextDefinition("string",
 *       label = @Translation("Text to concatenate")
 *     ),
 *   },
 *   configure_permissions = { "access test configuration" },
 *   provides = {
 *     "concatenated" = @ContextDefinition("string",
 *       label = @Translation("Concatenated result"),
 *       description = @Translation("The concatenated text.")
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_test_string",
  label: new TranslatableMarkup("Test concatenate action"),
  category: new TranslatableMarkup("Tests"),
  context_definitions: [
    "text" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Text to concatenate")
    ),
  ],
  configure_permissions: [
    "access test configuration",
  ],
  provides: [
    "concatenated" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Concatenated result"),
      description: new TranslatableMarkup("The concatenated text.")
    ),
  ]
)]
class TestStringAction extends RulesActionBase {

  /**
   * Concatenates the text with itself.
   *
   * @param string $text
   *   The text to concatenate.
   */
  protected function doExecute($text) {
    $this->setProvidedValue('concatenated', $text . $text);
  }

}
