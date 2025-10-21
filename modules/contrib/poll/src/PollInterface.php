<?php

namespace Drupal\poll;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining an poll entity.
 */
interface PollInterface extends ContentEntityInterface {

  /**
   * Denotes that the poll is not published.
   */
  const NOT_PUBLISHED = 0;

  /**
   * Denotes that the poll is published.
   */
  const PUBLISHED = 1;

  /**
   * Order votes by weight (default).
   */
  const VOTES_ORDER_WEIGHT = 0;

  /**
   * Order votes by count (ascending).
   */
  const VOTES_ORDER_COUNT_ASC = 1;

  /**
   * Order votes by count (descending).
   */
  const VOTES_ORDER_COUNT_DESC = 2;

  /**
   * Setting to allow only one anonymous vote per ip address.
   *
   * @var string
   */
  const ANONYMOUS_VOTE_RESTRICT_IP = 'ip';

  /**
   * Setting to allow only one anonymous vote per user session.
   *
   * @var string
   */
  const ANONYMOUS_VOTE_RESTRICT_SESSION = 'session';

  /**
   * Setting to allow anonymous users to vote multiple times.
   *
   * @var string
   */
  const ANONYMOUS_VOTE_RESTRICT_NONE = 'unlimited';

  /**
   * Sets the question for the poll.
   *
   * @param string $question
   *   The question of the poll.
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setQuestion($question);

  /**
   * Return when the poll was modified last time.
   *
   * @return int
   *   The timestamp of the last time the poll was modified.
   */
  public function getCreated();

  /**
   * Sets the last modification of the poll.
   *
   * @param int $created
   *   The timestamp when the poll was modified.
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setCreated($created);

  /**
   * Returns the runtime of the poll in seconds.
   *
   * @return int
   *   The refresh rate of the poll in seconds.
   */
  public function getRuntime();

  /**
   * Sets the runtime of the poll in seconds.
   *
   * @param int $runtime
   *   The refresh rate of the poll in seconds.
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setRuntime(int $runtime);

  /**
   * Return if an anonymous user is allowed to vote.
   *
   * @return bool
   *   True if allowed, false otherwise.
   */
  public function getAnonymousVoteAllow();

  /**
   * Sets if an anonymous user is allowed to vote.
   *
   * @param bool $anonymous_vote_allow
   *   True if allowed, false otherwise.
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setAnonymousVoteAllow($anonymous_vote_allow);

  /**
   * Returns if the user is allowed to cancel their vote.
   *
   * @return bool
   *   True if allowed, false otherwise.
   */
  public function getCancelVoteAllow();

  /**
   * Sets if the user is allowed to cancel their vote.
   *
   * @param bool $cancel_vote_allow
   *   True if allowed, false otherwise.
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setCancelVoteAllow($cancel_vote_allow);

  /**
   * Returns if the user is allowed to view the poll results.
   *
   * @return bool
   *   True if allowed, false otherwise.
   */
  public function getResultVoteAllow();

  /**
   * Sets if the user is allowed to view the poll results.
   *
   * @param bool $result_vote_allow
   *   True if allowed, false otherwise.
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setResultVoteAllow($result_vote_allow);

  /**
   * Returns if the poll is open.
   *
   * @return bool
   *   TRUE if the poll is open.
   */
  public function isOpen();

  /**
   * Returns if the poll is closed.
   *
   * @return bool
   *   TRUE if the poll is closed.
   */
  public function isClosed();

  /**
   * Sets the poll to closed.
   */
  public function close();

  /**
   * Sets the poll to open.
   */
  public function open();

  /**
   * Returns whether or not auto submit should be used in the voting form.
   *
   * @return bool
   *   Whether or not auto submit should be used in the voting form.
   */
  public function getAutoSubmit();

  /**
   * Sets whether or not auto submit should be used in the voting form.
   *
   * @param bool $submit
   *   Whether or not the poll should have auto submit enabled.
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setAutoSubmit($submit);

  /**
   * Returns the vote restriction that applies for anonymous users.
   *
   * See also the class constants that start with "ANONYMOUS_VOTE_RESTRICT_".
   *
   * @return string
   *   The vote restriction for anonymous users. Possible values:
   *   - ip: only one vote per ip address is allowed;
   *   - session: only one vote per user session is allowed;
   *   - unlimited: no restrictions apply. Anonymous users can place multiple
   *     votes for the same poll.
   *
   * @see ::ANONYMOUS_VOTE_RESTRICT_IP
   * @see ::ANONYMOUS_VOTE_RESTRICT_SESSION
   * @see ::ANONYMOUS_VOTE_RESTRICT_NONE
   */
  public function getVoteRestriction();

  /**
   * Returns whether the user has voted for this poll.
   *
   * @return array|false
   *   An associative array of vote data when available, or FALSE.
   *
   * @todo Refactor - doesn't belong here.
   */
  public function hasUserVoted();

  /**
   * Get all options for this poll.
   *
   * @return array
   *   Associative array of option keys and values.
   */
  public function getOptions();

  /**
   * Get the values of each vote option for this poll.
   *
   * @return array
   *   Associative array of option values.
   */
  public function getOptionValues();

  /**
   * Get all the votes of this poll.
   *
   * @return array
   *   An associative array of vote data keyed by choice id.
   */
  public function getVotes();

  /**
   * Get votes order type.
   *
   * @return int
   *   Votes order type. One of the following values:
   *    - PollInterface::VOTES_ORDER_WEIGHT: Order votes by weight (default).
   *    - PollInterface::VOTES_ORDER_COUNT_ASC: Order votes by count
   *      (ascending).
   *    - PollInterface::VOTES_ORDER_COUNT_DESC: Order votes by count
   *      (descending).
   */
  public function getVotesOrderType();

  /**
   * Sets votes order type.
   *
   * @param int $order_type
   *   One of the following values:
   *    - PollInterface::VOTES_ORDER_WEIGHT: Order votes by weight (default).
   *    - PollInterface::VOTES_ORDER_COUNT_ASC: Order votes by count
   *      (ascending).
   *    - PollInterface::VOTES_ORDER_COUNT_DESC: Order votes by count
   *      (descending).
   *
   * @return \Drupal\poll\PollInterface
   *   The class instance that this method is called on.
   */
  public function setVotesOrderType(int $order_type);

  /**
   * Checks if the current user is allowed to cancel on the given poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   A poll.
   *
   * @return bool
   *   TRUE if the user can cancel.
   */
  public function isCancelAllowed(PollInterface $poll): bool;

  /**
   * Checks if the current user is allowed to vote on the given poll.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll to check.
   *
   * @return bool
   *   True if the user can vote, false otherwise.
   */
  public function isVotingAllowed(PollInterface $poll): bool;

}
