<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Session\AccountInterface;

/**
 * Options provider for the types of field access to check for.
 */
class ConvertTypeOptions extends OptionsProviderBase {

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    return [
      'float' => $this->t('Float'),
      'integer' => $this->t('Integer'),
      'string' => $this->t('String'),
    ];
  }

}
