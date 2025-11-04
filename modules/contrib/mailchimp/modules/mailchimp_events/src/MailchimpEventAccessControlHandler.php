<?php

namespace Drupal\mailchimp_events;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access controller for the Mailchimp event entity.
 *
 * @see \Drupal\mailchimp_events\Entity\MailchimpEvent.
 */
class MailchimpEventAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\mailchimp_events\Entity\MailchimpEventInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished Mailchimp event types');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published Mailchimp event types');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit mailchimp event types');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete mailchimp event types');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add mailchimp event types');
  }

  /**
   * {@inheritdoc}
   */
  public function overview() {
    return [
      'description' => [
        '#markup' => 'Hey cool',
      ],
    ];
  }

}
