<?php

namespace Drupal\poll\Plugin\migrate\destination;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\migrate\destination\DestinationBase;
use Drupal\migrate\Row;
use Drupal\poll\PollVoteStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Destination plugin to migrate Poll votes.
 *
 * @MigrateDestination(
 *   id = "poll_vote"
 * )
 */
class PollVote extends DestinationBase implements ContainerFactoryPluginInterface {

  /**
   * The poll vote storage service.
   *
   * @var \Drupal\poll\PollVoteStorageInterface
   */
  protected $pollVoteStorage;

  /**
   * Constructs an entity destination plugin.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\migrate\Plugin\MigrationInterface $migration
   *   The migration.
   * @param \Drupal\poll\PollVoteStorageInterface $pollVoteStorage
   *   The poll vote storage service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration, PollVoteStorageInterface $pollVoteStorage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);
    $this->pollVoteStorage = $pollVoteStorage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, ?MigrationInterface $migration = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $migration,
      $container->get('poll_vote.storage')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $vote = [];
    $vote['chid'] = $row->getDestinationProperty('chid');
    $vote['pid'] = $row->getDestinationProperty('pid');
    $vote['uid'] = $row->getDestinationProperty('uid');
    $vote['hostname'] = $row->getDestinationProperty('hostname');
    $vote['timestamp'] = $row->getDestinationProperty('timestamp');

    $this->pollVoteStorage->saveVote($vote);
    return [$vote['chid'], $vote['uid'], $vote['timestamp']];
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

  /**
   * {@inheritdoc}
   */
  public function fields(?MigrationInterface $migration = NULL) {
    $fields = [
      'chid' => $this->t("The user's vote for this poll"),
      'uid' => $this->t('user ID for authenticated user'),
      'pid' => $this->t('user Poll ID that this vote was cast on'),
      'hostname' => $this->t('The ip address this vote is from.'),
      'timestamp' => $this->t('The timestamp of the vote creation.'),
    ];
    return $fields;
  }

}
