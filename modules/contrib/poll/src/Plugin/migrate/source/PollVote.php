<?php

namespace Drupal\poll\Plugin\migrate\source;

use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Gets the poll votes data from the source database.
 *
 * @MigrateSource(
 *   id = "poll_vote",
 *   source_module = "poll"
 * )
 */
class PollVote extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Select poll in its last revision.
    $query = $this->select('poll_vote', 'pv')
      ->fields('pv')
      ->orderBy('chid', 'ASC');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'chid' => $this->t("The user's vote for this poll"),
      'uid' => $this->t('user ID for authenticated user'),
      'nid' => $this->t('user Node ID that this vote was cast on'),
      'hostname' => $this->t('The ip address this vote is from.'),
      'timestamp' => $this->t('The timestamp of the vote creation.'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['chid']['type'] = 'integer';
    $ids['uid']['type'] = 'integer';
    $ids['timestamp']['type'] = 'integer';
    return $ids;
  }

}
