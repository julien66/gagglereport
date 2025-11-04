<?php

namespace Drupal\rules\Plugin\RulesExpression;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Attribute\RulesExpression;
use Drupal\rules\Context\ExecutionStateInterface;
use Drupal\rules\Engine\ActionExpressionContainer;
use Drupal\rules\Form\Expression\ActionContainerForm;

/**
 * Holds a set of actions and executes all of them.
 *
 * @RulesExpression(
 *   id = "rules_action_set",
 *   label = @Translation("Action set"),
 *   form_class = "\Drupal\rules\Form\Expression\ActionContainerForm"
 * )
 */
#[RulesExpression(
  id: "rules_action_set",
  label: new TranslatableMarkup("Action set"),
  form_class: ActionContainerForm::class
)]
class ActionSetExpression extends ActionExpressionContainer {

  /**
   * {@inheritdoc}
   */
  protected function allowsMetadataAssertions() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function executeWithState(ExecutionStateInterface $state) {
    // Use the iterator to ensure the actions are sorted.
    foreach ($this as $action) {
      /** @var \Drupal\rules\Engine\ExpressionInterface $action */
      $action->executeWithState($state);
    }
  }

}
