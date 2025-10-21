<?php

namespace Drupal\Tests\poll\Functional;

/**
 * Tests the poll admin view.
 *
 * @group poll
 */
class PollViewTest extends PollTestBase {

  /**
   * Test with/without permission and with/without a poll.
   */
  public function testAdminView() {
    $this->drupalGet('admin/content/poll');
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->pageTextContains($this->poll->label());

    // Delete all polls, make sure that we get an empty message.
    $this->poll->delete();

    $this->drupalGet('admin/content/poll');
    $this->assertSession()->pageTextContains('No polls are available.');

    $test_user = $this->createUser(['access administration pages']);
    $this->drupalLogin($test_user);
    $this->drupalGet('admin/content/poll');
    $this->assertSession()->statusCodeEquals(403);
  }

}
