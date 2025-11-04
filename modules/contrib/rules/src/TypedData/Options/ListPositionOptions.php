<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider for the types of field access to check for.
 */
class ListPositionOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      'start' => $this->t('At the start'),
      'end' => $this->t('At the end'),
    ];
  }

}
