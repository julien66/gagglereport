<?php

namespace Drupal\poll\Plugin\migrate\source;

use Drupal\migrate\Row;
use Drupal\migrate_drupal\Plugin\migrate\source\DrupalSqlBase;

/**
 * Gets the poll data from the source database.
 *
 * @MigrateSource(
 *   id = "poll",
 *   source_module = "poll"
 * )
 */
class Poll extends DrupalSqlBase {

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Select poll in its last revision.
    $query = $this->select('node', 'n')
      ->fields('n')
      ->fields('p', [
        'runtime',
        'active',
      ]);
    $query->innerJoin('poll', 'p', 'n.nid = p.nid');
    $query->condition('n.type', 'poll');
    $query->orderBy('n.nid', 'ASC');

    return $query;
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    $fields = [
      'nid' => $this->t('Node ID'),
      'vid' => $this->t('revision ID'),
      'type' => $this->t('Type'),
      'title' => $this->t('Title'),
      'uid' => $this->t('Node authored by (uid)'),
      'created' => $this->t('Created timestamp'),
      'changed' => $this->t('Modified timestamp'),
      'status' => $this->t('Published'),
      'promote' => $this->t('Promoted to front page'),
      'sticky' => $this->t('Sticky at top of lists'),
      'language' => $this->t('Language (fr, en, ...)'),
      'runtime' => $this->t('Poll runtime'),
      'active' => $this->t('Poll Active status'),
    ];
    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    $ids['nid']['type'] = 'integer';
    $ids['nid']['alias'] = 'n';
    $ids['vid']['type'] = 'integer';
    $ids['vid']['alias'] = 'n';
    return $ids;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

    $choices = [];

    $results = $this->select('poll_choice', 'pc')
      ->fields('pc', [
        'chid',
        'nid',
        'chtext',
        'weight',
      ])
      ->condition('pc.nid', $row->getSourceProperty('nid'), '=')
      ->orderBy('weight', 'ASC')
      ->execute()
      ->fetchAll();
    if (!empty($results)) {
      foreach ($results as $result) {
        $choices[] = [
          'chid' => $result['chid'],
        ];
      }
    }

    // Set choices array on \Drupal\migrate\Row with values of choice ID "chid".
    // This will allow the subprocess plugin to iterate over the values
    // and add the choice ID's to the poll_question migration.
    $row->setSourceProperty('choices', $choices);

    return parent::prepareRow($row);
  }

}
