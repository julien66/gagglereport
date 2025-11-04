<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return a Yes / No choice.
 */
class YesNoOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      0 => $this->t('No'),
      1 => $this->t('Yes'),
    ];
  }

}
