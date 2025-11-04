<?php

namespace Drupal\rules_test\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides an always-FALSE test condition.
 *
 * @Condition(
 *   id = "rules_test_false",
 *   label = @Translation("Test condition returning false"),
 *   category = @Translation("Tests")
 * )
 */
#[Condition(
  id: "rules_test_false",
  label: new TranslatableMarkup("Test condition returning false"),
  category: new TranslatableMarkup("Tests")
)]
class TestConditionFalse extends RulesConditionBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // We don't care about summaries for test condition plugins.
    return '';
  }

}
