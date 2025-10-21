<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Tests creating a poll.
 *
 * @group poll
 */
class PollCreateTest extends PollTestBase {

  use StringTranslationTrait;

  /**
   * A user with access to view polls but without administrative permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $siteBuilder;

  /**
   * Tests creating and editing a poll.
   */
  public function testPollCreate() {

    $poll = $this->poll;

    // Check we loaded the right poll.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $this->assertSession()->pageTextContains($poll->label());

    // Verify applying condition for non-active polls.
    $this->drupalGet('admin/content/poll', ['query' => ['active' => '2']]);
    $this->assertSession()->pageTextNotContains($poll->label());

    // Verify poll appears on 'poll' page.
    $this->drupalGet('admin/content/poll');
    $this->assertSession()->pageTextContains($poll->label());
    $this->assertSession()->pageTextContains('Y');

    // Click on the poll question to go to poll page.
    $this->clickLink($poll->label());

    // Alter the question and ensure it gets saved correctly.
    $new_question = $this->randomMachineName();
    $poll->setQuestion($new_question);
    $poll->save();

    // Check the new question has taken effect.
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $this->assertSession()->pageTextContains($new_question);

    // Now add a new option to make sure that when we update the poll, the
    // option is displayed.
    $vote_choice = $this->randomMachineName();
    $poll->choice[0]->entity->setChoice($vote_choice);
    $poll->choice[0]->entity->save();

    // Check the new choice has taken effect.
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $this->assertSession()->fieldValueEquals('choice[0][choice]', $vote_choice);

    // Verify applying condition for non-published polls.
    $poll->setUnpublished();
    $poll->save();
    $this->drupalGet('admin/content/poll', ['query' => ['status' => '1']]);
    $this->assertSession()->pageTextNotContains($poll->label());

  }

  /**
   * Tests creating, editing, and closing a poll.
   */
  public function testPollClose() {

    $poll = $this->poll;
    $poll->close();
    $poll->save();

    $this->drupalLogin($this->webUser);

    // Poll create disallowed.
    $this->drupalGet('poll/add');
    $this->assertSession()->statusCodeEquals(403);

    // Get a poll.
    $this->drupalGet('poll/' . $poll->id());

    // Verify 'Vote' button no longer appears.
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(empty($elements), "Vote button doesn't appear.");

    // Verify 'View Poll' button no longer appears.
    $elements = $this->xpath('value="View poll"');
    $this->assertTrue(empty($elements), "View poll button doesn't appear.");

    // Edit the poll and re-activate.
    $poll->open();
    $poll->save();
    $this->drupalGet('poll/' . $poll->id());

    // Verify 'Vote' button not appears.
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertFalse(empty($elements), 'Vote button appears.');

    // Check to see if the vote was recorded and that the user may cancel their
    // vote.
    $edit = [
      'choice' => '1',
    ];
    $this->submitForm($edit, 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $elements = $this->xpath('//input[@value="Cancel vote"]');
    $this->assertTrue(isset($elements[0]), "'Cancel vote' button appears.");

    // Verify 'Cancel your vote' button no longer appears after poll is closed.
    $poll->close();
    $poll->save();
    $this->drupalGet('poll/' . $poll->id());
    $elements = $this->xpath('//input[@value="Cancel your vote"]');
    $this->assertTrue(empty($elements), "'Cancel your vote' button no longer appears.");
  }

  /**
   * Poll create with restricted user.
   */
  public function testWithRestrictedUser() {
    $admin_poll = $this->poll;
    // Create and login restricted user.
    $account = $this->drupalCreateUser([
      'create polls',
    ]);
    $this->drupalLogin($account);
    // Create poll allowed.
    $this->drupalGet('poll/add');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->fieldNotExists('uid[0][target_id]');
    // Create poll and test edit.
    $own_poll = $this->pollCreate(7, $account);
    $this->drupalGet('poll/' . $admin_poll->id() . '/edit');
    $this->assertSession()->statusCodeEquals(403);
    $this->drupalGet('poll/' . $own_poll->id() . '/edit');
    $this->assertSession()->statusCodeEquals(403);
    // Test another user with "edit own poll" permission.
    $account = $this->drupalCreateUser([
      'create polls',
      'edit own polls',
    ]);
    $this->drupalLogin($account);
    $own_poll = $this->pollCreate(7, $account);
    $this->drupalGet('poll/' . $admin_poll->id() . '/edit');
    $this->assertSession()->statusCodeEquals(403);
    $this->drupalGet('poll/' . $own_poll->id() . '/edit');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->fieldNotExists('uid[0][target_id]');
  }

  /**
   * Tests edit permissions.
   */
  public function testEditAnyPollsPermission() {
    $this->siteBuilder = $this->drupalCreateUser(array_merge([
      'edit any polls',
    ], $this->webUserPermissions));

    // User has "edit any polls" permission.
    $this->drupalLogin($this->siteBuilder);
    $this->drupalGet('poll/' . $this->poll->id() . '/edit');
    $this->assertSession()->statusCodeEquals(200);

    // User has "administer polls" permission.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $this->poll->id() . '/edit');
    $this->assertSession()->statusCodeEquals(200);

    // User does not have edit permission.
    $this->drupalLogin($this->webUser);
    $this->drupalGet('poll/' . $this->poll->id() . '/edit');
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Test unpublished poll.
   */
  public function testPollUnpublished() {

    $poll = $this->poll;
    $poll->save();

    $this->drupalLogin($this->webUser);

    // Get a poll.
    $this->drupalGet('poll/' . $poll->id());

    // Verify 'View Poll' button appears.
    $elements = $this->xpath('value="View poll"');
    $this->assertTrue(empty($elements), "View poll button doesn't appear.");

    // Unpublish the poll.
    $poll->setUnpublished();
    $poll->save();

    // Get a poll.
    $this->drupalGet('poll/' . $poll->id());

    // Verify 'View Poll' button no longer appears.
    $elements = $this->xpath('value="View poll"');
    $this->assertTrue(empty($elements), "View poll button doesn't appear.");

    // Test another user with "Access unpublished polls" permission.
    $account = $this->drupalCreateUser([
      'access unpublished polls',
    ]);
    $this->drupalLogin($account);

    // Get a poll.
    $this->drupalGet('poll/' . $poll->id());

    // Verify 'View Poll' button appears.
    $elements = $this->xpath('value="View poll"');
    $this->assertTrue(empty($elements), "View poll button doesn't appear.");
  }

}
