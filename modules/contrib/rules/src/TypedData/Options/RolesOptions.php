<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

/**
 * Options provider to return a list of user roles.
 */
class RolesOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    // All roles, including 'Anonymous'.
    $roles = array_map(function (RoleInterface $role) {
      return $role->label();
    }, Role::loadMultiple());

    // Sort by the role name.
    asort($roles);

    return $roles;
  }

}
