<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\user\UserInterface;

/**
 * Provides a 'User is blocked' condition.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @Condition(
 *   id = "rules_user_is_blocked",
 *   label = @Translation("User is blocked"),
 *   category = @Translation("User"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user account to check.")
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_user_is_blocked",
  label: new TranslatableMarkup("User is blocked"),
  category: new TranslatableMarkup("User"),
  context_definitions: [
    "user" => new ContextDefinition(
      data_type: "entity:user",
      label: new TranslatableMarkup("User"),
      description: new TranslatableMarkup("Specifies the user account to check.")
    ),
  ]
)]
class UserIsBlocked extends RulesConditionBase {

  /**
   * Check if user is blocked.
   *
   * @param \Drupal\user\UserInterface $user
   *   The account to check.
   *
   * @return bool
   *   TRUE if the account is blocked.
   */
  protected function doEvaluate(UserInterface $user) {
    return $user->isBlocked();
  }

}
