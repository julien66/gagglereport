<?php

namespace Drupal\mailchimp_campaign;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Access control handler for the MailchimpCampaign entity.
 */
class MailchimpCampaignAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function access(EntityInterface $entity, $operation, ?AccountInterface $account = NULL, $return_as_object = FALSE) {
    /** @var \Drupal\mailchimp_campaign\Entity\MailchimpCampaign $entity */

    // Ensure the associated audience still exists.
    if (!$entity->mc_data) {
      \Drupal::messenger()->addError($this->t('Data for this campaign is missing. Were the audiences deleted? Were settings changed?'), 'error');
      return parent::access($entity, $operation, $account, $return_as_object);
    }

    $status = $entity->mc_data->status;
    $return = NULL;
    switch ($operation) {
      case 'send':
      case 'sendtestmail':
      case 'edit':
      case 'delete':
        $return = ($status == MAILCHIMP_STATUS_SENT) ? AccessResult::forbidden() : AccessResult::allowed();
        break;

      case 'stats':
        $return = ($status == MAILCHIMP_STATUS_SENT) ? AccessResult::allowed() : AccessResult::forbidden();
        break;

      default:
        $return = parent::access($entity, $operation, $account, $return_as_object);
    }
    return $return;
  }

}
