<?php

namespace Drupal\rules_test\Plugin\Condition;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides an always-TRUE test condition.
 *
 * @Condition(
 *   id = "rules_test_true",
 *   label = @Translation("Test condition returning true"),
 *   category = @Translation("Tests")
 * )
 */
#[Condition(
  id: "rules_test_true",
  label: new TranslatableMarkup("Test condition returning true"),
  category: new TranslatableMarkup("Tests")
)]
class TestConditionTrue extends RulesConditionBase {

  /**
   * {@inheritdoc}
   */
  public function evaluate() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // We don't care about summaries for test condition plugins.
    return '';
  }

}
