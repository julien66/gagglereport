<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider for the types of user account email to send.
 */
class EmailTypeOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      'register_admin_created' => $this->t('Welcome message for user created by the admin'),
      'register_no_approval_required' => $this->t('Welcome message when user self-registers'),
      'register_pending_approval' => $this->t('Welcome message, user pending admin approval'),
      'password_reset' => $this->t('Password recovery request'),
      'status_activated' => $this->t('Account activated'),
      'status_blocked' => $this->t('Account blocked'),
      'cancel_confirm' => $this->t('Account cancellation request'),
      'status_canceled' => $this->t('Account canceled'),
    ];
  }

}
