<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\Database\Database;
use Drupal\Core\Session\AccountInterface;
use Drupal\poll\PollInterface;

/**
 * Tests anonymous voting on a poll.
 *
 * @group poll
 */
class PollAnonymousVoteTest extends PollTestBase {

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // Allow anonymous users to vote on polls.
    user_role_change_permissions(AccountInterface::ANONYMOUS_ROLE, [
      'cancel own vote' => TRUE,
      'access polls' => TRUE,
    ]);

    $this->poll->setAnonymousVoteAllow(TRUE)->save();

    $this->drupalLogout();
  }

  /**
   * Resets the cookie file so that it refers to the specified user.
   */
  protected function sessionReset() {
    $this->getSession()->setCookie($this->getSessionName());
  }

  /**
   * Tests voting using the one vote per ip restriction.
   */
  public function testOneVotePerIp() {
    // Place a vote as an anonymous user.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    // Ensure that the user cannot vote again.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->buttonNotExists('Vote');

    // Ensure that after a session reset, the user can still not vote again.
    $this->sessionReset();
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertEquals($this->getSession()->getResponseHeader('x-drupal-cache'), 'HIT', 'Page was cacheable but was not in the cache.');
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote for this poll has already been submitted.');

    // Change the IP address of the existing vote and check that the current
    // user can then vote again.
    Database::getConnection()->update('poll_vote')
      ->fields(['hostname' => '240.0.0.1'])
      ->condition('uid', \Drupal::currentUser()->id())
      ->execute();

    // And vote.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 2');
  }

  /**
   * Tests voting using the one vote per session restriction.
   */
  public function testOneVotePerSession() {
    // Set the restriction on the poll to 'session'.
    $this->poll->anonymous_vote_restriction = PollInterface::ANONYMOUS_VOTE_RESTRICT_SESSION;
    $this->poll->save();

    // Place a vote as an anonymous user.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    // Ensure that the user cannot vote again.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->buttonNotExists('Vote');

    // Start a new session.
    $this->sessionReset();

    // And ensure that the user can vote again.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 2');
  }

  /**
   * Tests voting using no restrictions.
   */
  public function testUnlimitedVotes() {
    // Set the restriction on the poll to 'unlimited'.
    $this->poll->anonymous_vote_restriction = PollInterface::ANONYMOUS_VOTE_RESTRICT_NONE;
    $this->poll->save();

    // Test that users can keep voting.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    // Click the "View poll" button in order to place another vote.
    $this->submitForm([], 'View poll');

    // And vote again.
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Total votes: 2');
    $this->assertSession()->pageTextNotContains('Your vote for this poll has already been submitted.');
  }

  /**
   * Tests that users can cancel their vote for each restriction.
   *
   * @dataProvider providerAnonymousVoteRestrictions
   */
  public function testCancelVote($restriction) {
    // Set the poll's restriction.
    $this->poll->anonymous_vote_restriction = $restriction;
    $this->poll->save();

    // Place a vote as an anonymous user.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    // Cancel the vote.
    $this->submitForm([], 'Cancel vote');
    $this->assertSession()->pageTextContains('Your vote was cancelled.');

    // Ensure that the user can vote again.
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');
  }

  /**
   * Data provider for ::testCancelVote().
   *
   * @return array
   *   A list of possible anonymous vote restrictions.
   */
  public function providerAnonymousVoteRestrictions() {
    return [
      [PollInterface::ANONYMOUS_VOTE_RESTRICT_IP],
      [PollInterface::ANONYMOUS_VOTE_RESTRICT_SESSION],
      [PollInterface::ANONYMOUS_VOTE_RESTRICT_NONE],
    ];
  }

}
