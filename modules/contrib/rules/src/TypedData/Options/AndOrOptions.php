<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return a choice of 'AND' or 'OR'.
 */
class AndOrOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      'AND' => $this->t('All selected (and)'),
      'OR' => $this->t('Any selected (or)'),
    ];
  }

}
