<?php

namespace Drupal\poll;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller class for polls.
 *
 * This extends the default content entity storage class,
 * adding required special handling for poll entities.
 */
class PollStorage extends SqlContentEntityStorage implements PollStorageInterface {

  /**
   * The poll vote storage service.
   *
   * @var \Drupal\poll\PollVoteStorageInterface
   */
  protected PollVoteStorageInterface $pollVoteStorage;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->pollVoteStorage = $container->get('poll_vote.storage');
    $instance->time = $container->get('datetime.time');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getTotalVotes(PollInterface $poll) {
    return $this->pollVoteStorage->getTotalVotes($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function deleteVotes(PollInterface $poll) {
    return $this->pollVoteStorage->deleteVotes($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function getUserVote(PollInterface $poll) {
    return $this->pollVoteStorage->getUserVote($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function saveVote(array $options) {
    return $this->pollVoteStorage->saveVote($options);
  }

  /**
   * {@inheritdoc}
   */
  public function getVotes(PollInterface $poll) {
    return $this->pollVoteStorage->getVotes($poll);
  }

  /**
   * {@inheritdoc}
   */
  public function cancelVote(PollInterface $poll, ?AccountInterface $account = NULL) {
    $this->pollVoteStorage->cancelVote($poll, $account);
  }

  /**
   * {@inheritdoc}
   */
  public function getPollDuplicates(PollInterface $poll) {
    $query = $this->entityTypeManager->getStorage('poll')->getQuery();
    $query->accessCheck(TRUE);
    $query->condition('question', $poll->label());

    if ($poll->id()) {
      $query->condition('id', $poll->id(), '<>');
    }
    return $this->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  public function getMostRecentPoll() {
    $query = $this->entityTypeManager->getStorage('poll')->getQuery();
    $query->accessCheck(TRUE);
    $query->condition('status', PollInterface::PUBLISHED)
      ->sort('created', 'DESC')
      ->pager(1);
    return $this->loadMultiple($query->execute());
  }

  /**
   * {@inheritdoc}
   */
  public function getExpiredPolls() {
    $query = $this->database->query('SELECT id FROM {poll_field_data} WHERE (:timestamp > (created + runtime)) AND status = 1 AND runtime <> 0', [':timestamp' => $this->time->getCurrentTime()]);
    return $this->loadMultiple($query->fetchCol());
  }

}
