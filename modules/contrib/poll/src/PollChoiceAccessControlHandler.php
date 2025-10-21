<?php

namespace Drupal\poll;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access control handler for the poll_choice entity.
 *
 * @see \Drupal\poll\Entity\PollChoice
 */
class PollChoiceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // Allow view access if the user has the access polls permission.
    if ($operation == 'view') {
      return AccessResult::allowedIfHasPermission($account, 'access polls');
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
