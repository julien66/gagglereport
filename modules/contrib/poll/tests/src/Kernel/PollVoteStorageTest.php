<?php

namespace Drupal\Tests\poll\Kernel;

use Drupal\Tests\user\Traits\UserCreationTrait;

/**
 * Tests the PollVoteStorage class.
 *
 * @group poll
 *
 * @coversDefaultClass \Drupal\poll\PollVoteStorage
 */
class PollVoteStorageTest extends PollKernelTestBase {

  use UserCreationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['system'];

  /**
   * @covers ::getUserVote
   */
  public function testGetUserVote() {
    $poll = $this->createPoll();
    $choice_id = $poll->get('choice')->first()->getValue()['target_id'];
    $this->saveVote($poll, $choice_id);

    /** @var \Drupal\poll\PollVoteStorageInterface $poll_vote_storage */
    $poll_vote_storage = $this->container->get('poll_vote.storage');

    $this->assertEquals($choice_id, $poll_vote_storage->getUserVote($poll)['chid']);

    // Test that a second poll returns the vote for that poll and not the first.
    $second_poll = $this->createPoll();
    $another_choice_id = $second_poll->get('choice')->first()->getValue()['target_id'];
    $this->saveVote($second_poll, $another_choice_id);
    $this->assertEquals($another_choice_id, $poll_vote_storage->getUserVote($second_poll)['chid']);

    // Test that anonymous users with non-anonymous poll return FALSE.
    $not_anonymous_poll = $this->createPoll();
    $not_anonymous_poll->setAnonymousVoteAllow(FALSE);
    $poll->save();
    $this->assertFalse($poll_vote_storage->getUserVote($not_anonymous_poll));

    // Test with an authenticated user.
    $this->setUpCurrentUser(['uid' => 2]);
    $choice_id = $poll->get('choice')->get(1)->target_id;
    $this->saveVote($poll, $choice_id);
    $this->assertEquals($choice_id, $poll_vote_storage->getUserVote($poll)['chid']);

    // Make sure we are using the stored value by removing the votes from the
    // database and testing that we still get a value from our service.
    /** @var \Drupal\Core\Database\Connection $connection */
    $connection = $this->container->get('database');
    $connection->delete('poll_vote')->execute();
    $this->assertEquals($choice_id, $poll_vote_storage->getUserVote($poll)['chid']);
  }

}
