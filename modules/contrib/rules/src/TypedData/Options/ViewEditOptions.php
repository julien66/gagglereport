<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider for the types of field access to check for.
 */
class ViewEditOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      'view' => $this->t('View'),
      'edit' => $this->t('Edit'),
    ];
  }

}
