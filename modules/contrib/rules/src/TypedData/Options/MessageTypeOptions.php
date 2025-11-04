<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return the System Message types.
 */
class MessageTypeOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      'info' => $this->t('Info'),
      'status' => $this->t('Status'),
      'warning' => $this->t('Warning'),
      'error' => $this->t('Error'),
    ];
  }

}
