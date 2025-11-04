<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider for the types of field access to check for.
 */
class RoundingOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      '' => $this->t('None'),
      'up' => $this->t('Up (ceiling)'),
      'down' => $this->t('Down (floor)'),
      'round' => $this->t('Round (nearest)'),
    ];
  }

}
