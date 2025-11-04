<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return a choice of data comparison operators.
 */
class ComparisonOperatorOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      '==' => $this->t('== (equals)'),
      '<' => $this->t('< (less than)'),
      '>' => $this->t('> (greater than)'),
      'contains' => $this->t('Contains (for strings or arrays)'),
      'in' => $this->t('In (for arrays or lists)'),
    ];
  }

}
