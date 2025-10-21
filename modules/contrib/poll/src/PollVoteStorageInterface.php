<?php

namespace Drupal\poll;

use Drupal\Core\Session\AccountInterface;

/**
 * Defines a common interface for poll vote controller classes.
 */
interface PollVoteStorageInterface {

  /**
   * Delete a user's votes for a poll choice.
   *
   * @param array $choices
   *   A list of choice ID's for each one we will remove all the votes.
   */
  public function deleteChoicesVotes(array $choices);

  /**
   * Delete all user votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   */
  public function deleteVotes(PollInterface $poll);

  /**
   * Cancel a user's vote for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   A user account.
   */
  public function cancelVote(PollInterface $poll, ?AccountInterface $account = NULL);

  /**
   * Save a user's vote.
   *
   * @param array $options
   *   An associative array of options keyed by poll_vote properties.
   *
   * @return int
   *   The ID of the saved vote.
   */
  public function saveVote(array $options);

  /**
   * Get all votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return array
   *   An associative array of vote data keyed by choice id.
   */
  public function getVotes(PollInterface $poll);

  /**
   * Get a user's votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return array|false
   *   An array of the user's vote values, or false if the current user hasn't
   *   voted yet.
   */
  public function getUserVote(PollInterface $poll);

  /**
   * Get total votes for a poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return int
   *   The total amount of votes for the poll.
   */
  public function getTotalVotes(PollInterface $poll);

}
