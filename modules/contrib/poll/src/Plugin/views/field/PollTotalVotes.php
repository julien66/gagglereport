<?php

namespace Drupal\poll\Plugin\views\field;

use Drupal\poll\Entity\Poll;
use Drupal\poll\PollVoteStorageInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Field handler which shows the total votes for a poll.
 *
 * @ViewsField("poll_totalvotes")
 */
class PollTotalVotes extends FieldPluginBase {

  /**
   * The poll vote storage service.
   *
   * @var \Drupal\poll\PollVoteStorageInterface
   */
  protected PollVoteStorageInterface $pollVoteStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->pollVoteStorage = $container->get('poll_vote.storage');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $entity = Poll::load($this->getValue($values));
    $build['#markup'] = $this->pollVoteStorage->getTotalVotes($entity);
    $build['#cache']['tags'][] = 'poll-votes:' . $entity->id();
    return $build;
  }

}
