<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\Exception\InvalidArgumentException;
use Drupal\rules\TypedData\Options\AndOrOptions;
use Drupal\rules\TypedData\Options\RolesOptions;
use Drupal\user\UserInterface;

/**
 * Provides a 'User has roles(s)' condition.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @Condition(
 *   id = "rules_user_has_role",
 *   label = @Translation("User has role(s)"),
 *   category = @Translation("User"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user account to check."),
 *     ),
 *     "roles" = @ContextDefinition("entity:user_role",
 *       label = @Translation("Roles"),
 *       description = @Translation("Specifies the roles to check for."),
 *       multiple = TRUE,
 *       options_provider = "\Drupal\rules\TypedData\Options\RolesOptions"
 *     ),
 *     "operation" = @ContextDefinition("string",
 *       label = @Translation("Matching multiple roles"),
 *       description = @Translation("Specify if the user must have <em>all</em> the roles selected or <em>any</em> of the roles selected."),
 *       assignment_restriction = "input",
 *       default_value = "AND",
 *       options_provider = "\Drupal\rules\TypedData\Options\AndOrOptions",
 *       required = FALSE
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_user_has_role",
  label: new TranslatableMarkup("User has role"),
  category: new TranslatableMarkup("User"),
  context_definitions: [
    "user" => new ContextDefinition(
      data_type: "entity:user",
      label: new TranslatableMarkup("User"),
      description: new TranslatableMarkup("Specifies the user account to check.")
    ),
    "roles" => new ContextDefinition(
      data_type: "entity:user_role",
      label: new TranslatableMarkup("Roles"),
      description: new TranslatableMarkup("Specifies the roles to check for."),
      multiple: TRUE,
      options_provider: RolesOptions::class
    ),
    "operation" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Matching multiple roles"),
      description: new TranslatableMarkup("Specify if the user must have <em>all</em> the roles selected or <em>any</em> of the roles selected."),
      assignment_restriction: "input",
      default_value: "AND",
      options_provider: AndOrOptions::class,
      required: FALSE
    ),
  ]
)]
class UserHasRole extends RulesConditionBase {

  /**
   * Evaluate if user has role(s).
   *
   * @param \Drupal\user\UserInterface $user
   *   The account to check.
   * @param \Drupal\user\RoleInterface[] $roles
   *   Array of user roles.
   * @param string $operation
   *   Either "AND": user has all of roles.
   *   Or "OR": user has at least one of all roles.
   *   Defaults to "AND".
   *
   * @return bool
   *   TRUE if the user has the role(s).
   */
  protected function doEvaluate(UserInterface $user, array $roles, $operation = 'AND') {

    $rids = array_map(function ($role) {
      return $role->id();
    }, $roles);

    switch (strtoupper($operation)) {
      case 'OR':
        return (bool) array_intersect($rids, $user->getRoles());

      case 'AND':
        return (bool) !array_diff($rids, $user->getRoles());

      default:
        throw new InvalidArgumentException('Either use "AND" or "OR". Leave empty for default "AND" behavior.');
    }
  }

}
