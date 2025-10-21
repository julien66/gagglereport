<?php

namespace Drupal\poll;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines a common interface for poll entity controller classes.
 */
interface PollStorageInterface extends EntityStorageInterface {

  /**
   * Save a user's vote.
   *
   * @param array $options
   *   An associative array of options keyed by poll_vote properties.
   *
   * @deprecated in poll:8.x-1.0 and is removed from poll:8.x-2.0. Use \Drupal\poll\PollVoteStorageInterface::saveVote() instead.
   *
   * @see https://www.drupal.org/node/2682423
   * @see \Drupal\poll\PollVoteStorageInterface::saveVote()
   */
  public function saveVote(array $options);

  /**
   * Cancel a user's vote for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user account.
   *
   * @deprecated in poll:8.x-1.0 and is removed from poll:8.x-2.0. Use \Drupal\poll\PollVoteStorageInterface::cancelVote() instead.
   *
   * @see https://www.drupal.org/node/2682423
   * @see \Drupal\poll\PollVoteStorageInterface::cancelVote()
   */
  public function cancelVote(PollInterface $poll, ?AccountInterface $account = NULL);

  /**
   * Get total votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return int
   *   The total amount of votes for the poll.
   *
   * @deprecated in poll:8.x-1.0 and is removed from poll:8.x-2.0. Use \Drupal\poll\PollVoteStorageInterface::getTotalVotes() instead.
   *
   * @see https://www.drupal.org/node/2682423
   * @see \Drupal\poll\PollVoteStorageInterface::getTotalVotes()
   */
  public function getTotalVotes(PollInterface $poll);

  /**
   * Get all votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return array
   *   An associative array of vote data keyed by choice id.
   *
   * @deprecated in poll:8.x-1.0 and is removed from poll:8.x-2.0. Use \Drupal\poll\PollVoteStorageInterface::getVotes() instead.
   *
   * @see https://www.drupal.org/node/2682423
   * @see \Drupal\poll\PollVoteStorageInterface::getVotes()
   */
  public function getVotes(PollInterface $poll);

  /**
   * Delete all user votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @deprecated in poll:8.x-1.0 and is removed from poll:8.x-2.0. Use \Drupal\poll\PollVoteStorageInterface::deleteVotes() instead.
   *
   * @see https://www.drupal.org/node/2682423
   * @see \Drupal\poll\PollVoteStorageInterface::deleteVotes()
   */
  public function deleteVotes(PollInterface $poll);

  /**
   * Get a user's votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return array|false
   *   An associative array of vote data when available, or FALSE.
   *
   * @deprecated in poll:8.x-1.0 and is removed from poll:8.x-2.0. Use \Drupal\poll\PollVoteStorageInterface::getUserVote() instead.
   *
   * @see https://www.drupal.org/node/2682423
   * @see \Drupal\poll\PollVoteStorageInterface::getUserVote()
   */
  public function getUserVote(PollInterface $poll);

  /**
   * Get the most recent poll posted on the site.
   *
   * @return \Drupal\poll\PollInterface[]
   *   An array of polls indexed by their ID.
   */
  public function getMostRecentPoll();

  /**
   * Find all duplicates of a poll by matching the question.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return \Drupal\poll\PollInterface[]
   *   An array of polls indexed by their ID.
   */
  public function getPollDuplicates(PollInterface $poll);

  /**
   * Returns all expired polls.
   *
   * @return \Drupal\poll\PollInterface[]
   *   An array of polls indexed by their ID.
   */
  public function getExpiredPolls();

}
