<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return a choice of numeric comparison operators.
 */
class ComparisonOperatorNumericOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      '==' => '== (equals)',
      '<'  => '< (less than)',
      '<=' => '<= (less than or equal to)',
      '>'  => '> (greater than)',
      '>=' => '>= (greater than or equal to)',
    ];
  }

}
