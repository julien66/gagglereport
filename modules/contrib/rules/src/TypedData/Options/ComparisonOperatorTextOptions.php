<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return a choice of text comparison operators.
 */
class ComparisonOperatorTextOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      'contains' => 'contains',
      'starts' => 'starts with',
      'ends' => 'ends with',
      'regex' => 'matches regex',
    ];
  }

}
