<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;

/**
 * Provides an 'Add a variable' action.
 *
 * @todo The context definition for "type" needs an options_provider list.
 *
 * @RulesAction(
 *   id = "rules_variable_add",
 *   label = @Translation("Add a variable"),
 *   category = @Translation("Data"),
 *   context_definitions = {
 *     "type" = @ContextDefinition("string",
 *       label = @Translation("Data Type"),
 *       description = @Translation("Specifies the type of the variable that should be added."),
 *       assignment_restriction = "input"
 *     ),
 *     "value" = @ContextDefinition("any",
 *       label = @Translation("Value"),
 *       description = @Translation("Optionally, specify the initial value of the variable."),
 *       required = FALSE
 *     ),
 *   },
 *   provides = {
 *     "variable_added" = @ContextDefinition("any",
 *       label = @Translation("Added variable")
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_variable_add",
  label: new TranslatableMarkup("Add a variable"),
  category: new TranslatableMarkup("Data"),
  context_definitions: [
    "type" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Data Type"),
      description: new TranslatableMarkup("Specifies the type of the variable that should be added."),
      assignment_restriction: "input"
    ),
    "value" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Value"),
      description: new TranslatableMarkup("Optionally, specify the initial value of the variable."),
      required: FALSE
    ),
  ],
  provides: [
    "variable_added" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Added variable")
    ),
  ]
)]
class VariableAdd extends RulesActionBase {

  /**
   * Add a variable.
   *
   * @param string $type
   *   The data type the new variable is of.
   * @param mixed $value
   *   The variable to add.
   */
  protected function doExecute($type, $value) {
    $this->setProvidedValue('variable_added', $value);
  }

  /**
   * {@inheritdoc}
   */
  public function refineContextDefinitions(array $selected_data) {
    if ($type = $this->getContextValue('type')) {
      $this->pluginDefinition['context_definitions']['value']->setDataType($type);
      $this->pluginDefinition['provides']['variable_added']->setDataType($type);
    }
  }

}
