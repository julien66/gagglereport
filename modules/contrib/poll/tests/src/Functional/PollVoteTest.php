<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\Database\Database;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\poll\PollInterface;
use Drupal\user\Entity\Role;
use Drupal\user\RoleInterface;

/**
 * Tests voting on a poll.
 *
 * @group poll
 */
class PollVoteTest extends PollTestBase {

  use StringTranslationTrait;

  /**
   * Tests voting on a poll.
   */
  public function testPollVote() {

    $this->drupalLogin($this->webUser);

    // Record a vote for the first choice.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel your vote' button appears.");
    $this->drupalGet('poll/' . $this->poll->id());

    // Cancel a vote.
    $this->submitForm([], 'Cancel vote');
    $this->assertSession()->pageTextContains('Your vote was cancelled.');
    $this->assertSession()->pageTextNotContains('Cancel your vote');
    $this->drupalGet('poll/' . $this->poll->id());

    // Empty vote on a poll.
    $this->submitForm([], 'Vote');
    $this->assertSession()->pageTextContains('Make a selection before voting.');
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(isset($elements[0]), "'Vote' button appears.");

    // Vote on a poll.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');
    $elements = $this->xpath('//input[@value="Cancel your vote"]');
    $this->assertTrue(empty($elements), "'Cancel your vote' button does not appear.");

    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/content/poll');
    $this->assertSession()->pageTextContains($this->poll->label());

    $assert_session = $this->assertSession();

    // Test for the overview page.
    $assert_session->elementContains('css', 'tbody tr:nth-child(1) td:nth-child(4)', 'Published');
    $assert_session->elementContains('css', 'tbody tr:nth-child(1) td:nth-child(5)', 'Active');
    $assert_session->elementContains('css', 'tbody tr:nth-child(1) td:nth-child(6)', 'No');

    // Edit the poll.
    $this->clickLink($this->poll->label());
    $this->clickLink('Edit');

    // Add the runtime date and allow anonymous to vote.
    $edit = [
      'runtime' => 172800,
      'anonymous_vote_allow[value]' => TRUE,
    ];

    $this->submitForm($edit, 'Save');

    // Assert that editing was successful.
    $this->assertSession()->pageTextContains('The poll ' . $this->poll->label() . ' has been updated.');
    $this->drupalGet('admin/content/poll');

    // Check if the active label is correct.
    $date = \Drupal::service('date.formatter')->format($this->poll->getCreated() + 172800, 'short');
    $output = 'Active (until ' . rtrim(strstr($date, '-', TRUE)) . ')';
    $assert_session->elementContains('css', 'tbody tr:nth-child(1) td:nth-child(5)', $output);

    // Check if allow anonymous voting is on.
    $assert_session->elementContains('css', 'tbody tr:nth-child(1) td:nth-child(6)', 'Yes');

    // Check the number of total votes.
    $assert_session->elementContains('css', 'tbody tr:nth-child(1) td:nth-child(2)', '1');

    // Add permissions to anonymous user to view polls.
    /** @var \Drupal\user\RoleInterface $anonymous_role */
    $anonymous_role = Role::load(RoleInterface::ANONYMOUS_ID);
    $anonymous_role->grantPermission('access polls');
    $anonymous_role->save();

    // Let the anonymous user to vote.
    $this->drupalLogout();
    $edit = ['choice' => '1'];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');

    // Login as admin and check the number of total votes on the overview page.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/content/poll');
    $this->assertSession()->elementContains('css', 'tr:nth-child(1) td.views-field.views-field-votes', 2);

    // Cancel the vote from the user, ensure that backend updates.
    $this->drupalLogin($this->webUser);
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm([], 'Cancel vote');
    $this->assertSession()->pageTextContains(t('Your vote was cancelled.'));

    // Login as admin and check the number of total votes on the overview page.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/content/poll');
    $this->assertSession()->elementContains('css', 'tr:nth-child(1) td.views-field.views-field-votes', 1);

    // Test for the 'View results' button.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="View results"]');
    $this->assertTrue(!empty($elements), "'View results' button appears.");

    $this->drupalLogin($this->webUser);
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="View results"]');
    $this->assertTrue(empty($elements), "'View results' button doesn't appear.");
  }

  /**
   * Test closed poll with "Cancel vote" button.
   */
  public function testClosedPollVoteCancel() {
    /** @var \Drupal\poll\PollInterface $poll */
    $poll = $this->pollCreate();
    $this->drupalLogin($this->webUser);
    $choices = $poll->choice->getValue();
    $this->drupalGet('poll/' . $poll->id());
    // Vote on a poll.
    $edit = [
      'choice' => $choices[0]['target_id'],
    ];
    $this->submitForm($edit, 'Vote');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel your vote' button appears.");
    // Close a poll.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $edit = [
      'status[value]' => FALSE,
    ];
    $this->submitForm($edit, 'Save');
    // Check closed poll with "Cancel vote" button.
    $this->drupalLogin($this->webUser);
    $this->drupalGet('poll/' . $poll->id());
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertFalse(isset($elements[0]), "'Cancel your vote' button not appears.");
  }

  /**
   * Test that anonymous user just remove it's own vote.
   */
  public function testAnonymousCancelVote() {
    // Now grant anonymous users permission to view the polls, vote and delete
    // it's own vote.
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, [
      'cancel own vote',
      'access polls',
    ]);
    $this->poll->setAnonymousVoteAllow(TRUE)->save();
    $this->drupalLogout();
    // First anonymous user votes.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');

    // Change the IP of first user.
    Database::getConnection()->update('poll_vote')
      ->fields(['hostname' => '240.0.0.1'])
      ->condition('uid', \Drupal::currentUser()->id())
      ->execute();

    // Logged in user votes.
    $this->drupalLogin($this->webUser);
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Total votes: 2');

    // Second anonymous user votes from same IP than the logged.
    $this->drupalLogout();
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Total votes: 3');

    // Second anonymous user cancels own vote.
    $this->submitForm([], 'Cancel vote');
    $this->drupalGet('poll/' . $this->poll->id());

    // Vote again to see the results, resulting in three votes again.
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Total votes: 3');
  }

  /**
   * Tests switching between viewing the poll and the poll results.
   */
  public function testViewPollAndPollResultsAsAuthenticatedUser() {
    $this->poll->setResultVoteAllow(TRUE);
    $this->poll->save();

    // Login as user who may vote.
    $this->drupalLogin($this->webUser);

    // Go to the poll form.
    $this->drupalGet('poll/' . $this->poll->id());

    // View the results.
    $this->submitForm([], 'View results');
    $this->assertSession()->pageTextContains('Total votes: 0');

    // Go back to the poll.
    $this->drupalGet('poll/' . $this->poll->id());

    // And vote.
    $edit = [
      'choice' => '1',
    ];
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');
  }

  /**
   * Tests switching between viewing the poll and the poll results.
   */
  public function testViewPollAndPollResultsAsAnonymousUser() {
    // Grant anonymous users permission to vote.
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, [
      'cancel own vote',
      'access polls',
    ]);
    $this->poll->setAnonymousVoteAllow(TRUE)
      ->setResultVoteAllow(TRUE)
      ->save();

    $this->drupalLogout();

    // Go the poll form.
    $this->drupalGet('poll/' . $this->poll->id());

    // View the results.
    $this->submitForm([], 'View results');
    $this->assertSession()->pageTextContains('Total votes: 0');

    // Go back to the poll.
    $this->submitForm([], 'View poll');

    // And vote.
    $edit = [
      'choice' => '1',
    ];
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');
  }

  /**
   * Verifies that poll actions contain the poll ID to ensure the ID is unique.
   */
  public function testPollActionIds() {
    $this->drupalLogin($this->webUser);
    $this->drupalGet('poll/' . $this->poll->id());

    // Verify the vote button ID.
    $elements = $this->xpath('//input[@id="edit-vote--' . $this->poll->id() . '"]');
    $this->assertTrue(isset($elements[0]), "Vote button has unique ID.");

    // Record a vote for the first choice.
    $edit = [
      'choice' => '1',
    ];
    $this->submitForm($edit, 'Vote');

    // Verify the cancel vote button.
    $elements = $this->xpath('//input[@id="edit-cancel--' . $this->poll->id() . '"]');
    $this->assertTrue(isset($elements[0]), "Cancel vote button has unique ID.");

    // Verify the view results button ID.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@id="edit-result--' . $this->poll->id() . '"]');
    $this->assertTrue(isset($elements[0]), "View results button has unique ID.");

    // Verify the back to poll button.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $this->poll->id());

    // Verify the back to poll button.
    $this->submitForm([], 'View results');
    $elements = $this->xpath('//input[@id="edit-back--' . $this->poll->id() . '"]');
    $this->assertTrue(isset($elements[0]), "View back to poll button has unique ID.");
  }

  /**
   * Verifies that the back button is only shown for admins when not voted.
   */
  public function testBackToPollButton() {
    user_role_grant_permissions(RoleInterface::ANONYMOUS_ID, [
      'access polls',
    ]);

    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $this->poll->id());

    // Admin user, not voted --> Button shown.
    $this->submitForm([], 'View results');
    $elements = $this->xpath('//input[@value="View poll"]');
    $this->assertTrue(!empty($elements), "'View poll' button appears.");

    // Admin user, voted --> Button not shown.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');

    $elements = $this->xpath('//input[@value="View poll"]');
    $this->assertFalse(!empty($elements), "'View poll' button does not appears.");

    // Anonymous user, no permission to vote --> Button not shown.
    $this->drupalLogout();
    $this->drupalGet('poll/' . $this->poll->id());
    $elements = $this->xpath('//input[@value="View poll"]');
    $this->assertFalse(!empty($elements), "'View poll' button does not appears.");

    // Set the poll to allow anonymous voting.
    $this->poll->setAnonymousVoteAllow(TRUE)->save();
    // Allow everyone to view the results before voting.
    $this->poll->setResultVoteAllow(TRUE)->save();

    // Anonymous user, allowed to view, not voted --> button shown.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm([], 'View results');
    $elements = $this->xpath('//input[@value="View poll"]');
    $this->assertTrue(!empty($elements), "'View poll' button appears.");

    // Anonymous user, permission to vote, voted --> button not shown.
    $edit = [
      'choice' => '1',
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');

    $elements = $this->xpath('//input[@value="View poll"]');
    $this->assertFalse(!empty($elements), "'View poll' button does not appears.");
  }

  /**
   * Test poll results order by vote count.
   */
  public function testPollResultsOrder() {
    $this->poll->setResultVoteAllow(TRUE);
    $this->poll->save();

    // Login as user who may vote.
    $this->drupalLogin($this->webUser);

    // Go to the poll.
    $this->drupalGet('poll/' . $this->poll->id());

    // And vote.
    $edit = [
      'choice' => '2',
    ];
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    // Get the second choice. This is a choice with vote.
    $second_choice = $this->getSession()->getPage()->find('xpath', '//form[@id="poll-view-form-1"]//dl//dd[2]//div[@class="percent"]');
    $this->assertEquals('100% (1 vote)', $second_choice->getText());

    // Set results sort by votes count asc.
    $this->poll->setVotesOrderType(PollInterface::VOTES_ORDER_COUNT_ASC);
    $this->poll->save();

    // Go to the poll.
    $this->drupalGet('poll/' . $this->poll->id());

    // Get the last choice. This is a choice with vote now.
    $last_choice = $this->getSession()->getPage()->find('xpath', '//form[@id="poll-view-form-1"]//dl//dd[7]//div[@class="percent"]');
    $this->assertEquals('100% (1 vote)', $last_choice->getText());

    // Set results sort by votes count desc.
    $this->poll->setVotesOrderType(PollInterface::VOTES_ORDER_COUNT_DESC);
    $this->poll->save();

    // Go to the poll.
    $this->drupalGet('poll/' . $this->poll->id());

    // Get the first choice. This is a choice with vote now.
    $first_choice = $this->getSession()->getPage()->find('xpath', '//form[@id="poll-view-form-1"]//dl//dd[1]//div[@class="percent"]');
    $this->assertEquals('100% (1 vote)', $first_choice->getText());
  }

}
