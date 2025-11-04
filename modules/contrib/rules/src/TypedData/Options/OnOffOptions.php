<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider to return an On / Off choice.
 */
class OnOffOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      0 => $this->t('Off'),
      1 => $this->t('On'),
    ];
  }

}
