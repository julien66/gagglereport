<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\Form\OptGroup;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\TypedData\OptionsProviderInterface;

/**
 * Base class for options providers used in Rules actions and conditions.
 */
abstract class OptionsProviderBase implements OptionsProviderInterface {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function getPossibleValues(AccountInterface $account = NULL) {
    // Flatten options firstly, because Possible Options may contain group
    // arrays.
    $flatten_options = OptGroup::flattenOptions($this->getPossibleOptions($account));
    return array_keys($flatten_options);
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableValues(AccountInterface $account = NULL) {
    return $this->getPossibleValues();
  }

  /**
   * {@inheritdoc}
   */
  public function getSettableOptions(AccountInterface $account = NULL) {
    return $this->getPossibleOptions();
  }

}
