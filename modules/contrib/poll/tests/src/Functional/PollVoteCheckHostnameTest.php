<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Check users and anonymous users from specified ip-address can only vote once.
 *
 * @group poll
 */
class PollVoteCheckHostnameTest extends PollTestBase {
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    // Allow anonymous users to vote on polls.
    user_role_change_permissions(AccountInterface::ANONYMOUS_ROLE, [
      // 'vote on polls' => TRUE,
      'cancel own vote' => TRUE,
      'access polls' => TRUE,
    ]);

    $this->poll->setAnonymousVoteAllow(TRUE)->save();
  }

  /**
   * Checks that anonymous users with the same IP address can only vote once.
   *
   * Also checks that authenticated users can only vote once, even when the
   * user's IP address has changed.
   */
  public function testHostnamePollVote() {

    $webUser2 = $this->drupalCreateUser(['access polls']);
    // Login User1.
    $this->drupalLogin($this->webUser);

    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());

    // $this->webUser->getUserName();
    // User1 vote on Poll.
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    // Check to make sure User1 cannot vote again.
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(empty($elements), $this->webUser->getAccountName() . " is not able to vote again.");
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Logout User1.
    $this->drupalLogout();

    // Fill the page cache by requesting the poll.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->responseHeaderEquals('x-drupal-cache', 'MISS');
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->responseHeaderEquals('x-drupal-cache', 'HIT');

    // Anonymous user vote on Poll.
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 2');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Check to make sure Anonymous user cannot vote again.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->responseHeaderDoesNotExist('x-drupal-cache');
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(empty($elements), "Anonymous is not able to vote again.");
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Login User2.
    $this->drupalLogin($webUser2);
    $this->drupalGet('poll/' . $this->poll->id());

    // User2 vote on poll.
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 3');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(empty($elements), "'Cancel vote' button does not appear.");

    // Logout User2.
    $this->drupalLogout();

    // Change host name for anonymous users.
    \Drupal::database()->update('poll_vote')
      ->fields([
        'hostname' => '123.456.789.1',
      ])
      ->condition('hostname', '', '<>')
      ->execute();

    // Check to make sure Anonymous user can vote again with a new session after
    // a hostname change.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->responseHeaderEquals('x-drupal-cache', 'HIT');
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 4');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");

    // Check to make sure Anonymous user cannot vote again with a new session,
    // and that the vote from the previous session cannot be cancelled. This
    // can't use drupalLogout() because we aren't actually logged in, so we
    // manually unset the session cookie.
    $this->getSession()->setCookie($this->getSessionName());
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->responseHeaderEquals('x-drupal-cache', 'HIT');
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote for this poll has already been submitted.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(empty($elements), "'Cancel vote' button does not appear.");

    // Login User1.
    $this->drupalLogin($this->webUser);

    // Check to make sure User1 still cannot vote even after hostname changed.
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(empty($elements), $this->webUser->getAccountName() . " is not able to vote again.");
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(!empty($elements), "'Cancel vote' button appears.");
  }

}
