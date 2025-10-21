<?php

namespace Drupal\Tests\poll\Kernel;

/**
 * Tests results generation for Polls.
 *
 * @group poll
 */
class PollResultsTest extends PollKernelTestBase {

  /**
   * Tests results generation for Polls.
   */
  public function testPollResults() {
    $poll = $this->createPoll();

    $this->saveVote($poll, $poll->get('choice')->target_id);
    $poll_results = $this->pollViewForm->showPollResults($poll);

    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    $user_vote = $vote_storage->getUserVote($poll);
    $this->assertEquals($user_vote['chid'], $poll_results['#vote']);
  }

}
