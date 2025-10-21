<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\poll\Entity\Poll;

/**
 * Tests the expiration of polls.
 *
 * @group poll
 */
class PollExpirationTest extends PollTestBase {

  /**
   * Tests the expiration of a poll.
   */
  public function testAutoExpire() {

    // Set up a poll.
    $poll = $this->poll;

    // Visit the poll edit page and verify that by default, expiration
    // is set to unlimited.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $this->assertSession()->fieldExists('runtime');
    $elements = $this->xpath('//select[@id="edit-runtime"]/option[@selected="selected"]');
    $this->assertEquals(0, $elements[0]->getAttribute('value'), 'Poll expiration set to unlimited.');

    // Set the expiration to one week.
    // One week.
    $runtime = 604800;
    $poll->setRuntime($runtime);
    $poll->save();

    // Make sure that the changed expiration settings is kept.
    // here.
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $elements = $this->xpath('//select[@id="edit-runtime"]/option[@selected="selected"]');
    $this->assertEquals($runtime, $elements[0]->getAttribute('value'), 'Poll expiration set to one week.');

    // Force a cron run. Since the expiration date has not yet been reached,
    // the poll should remain open.
    \Drupal::service('cron')->run();
    $this->assertTrue($poll->isOpen(), 'Poll remains open after cron.');

    $created = $poll->getCreated();
    $offset = $created - ($runtime * 1.01);
    $poll->setCreated($offset);
    $poll->save();

    // Run cron and verify that the poll is now marked as "closed".
    \Drupal::service('cron')->run();
    $loaded_poll = Poll::load($poll->id());
    $this->assertTrue($loaded_poll->isClosed(), 'Poll has expired.');
  }

}
