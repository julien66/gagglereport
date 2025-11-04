<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return a choice of numeric calculation operators.
 */
class CalculationOperatorOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      '+' => $this->t('+ (plus)'),
      '-' => $this->t('- (minus)'),
      '*' => $this->t('* (multiply)'),
      '/' => $this->t('/ (divide)'),
      'min' => $this->t('minimum of the two values'),
      'max' => $this->t('maximum of the two values'),
    ];
  }

}
