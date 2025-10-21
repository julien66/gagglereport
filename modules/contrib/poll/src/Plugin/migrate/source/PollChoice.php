<?php

namespace Drupal\poll\Plugin\migrate\source;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Gets the poll choices data from the source database.
 *
 * @MigrateSource(
 *   id = "poll_choice",
 *   source_module = "poll"
 * )
 */
class PollChoice extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Select poll choices.
    $query = $this->select('poll_choice', 'pc')
      ->fields('pc');
    $query->orderBy('chid', 'ASC');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'chid' => $this->t('Unique identifier of a poll choice.'),
      'chtext' => $this->t('Text of the Poll option'),
      'weight' => $this->t('The sort order of this choice among all choices'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['chid']['type'] = 'integer';
    return $ids;
  }

}
