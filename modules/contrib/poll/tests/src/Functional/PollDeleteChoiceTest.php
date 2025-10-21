<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Tests the removal of poll choices.
 *
 * @group poll
 */
class PollDeleteChoiceTest extends PollTestBase {

  use StringTranslationTrait;

  /**
   * Tests removing a choice from a poll.
   */
  public function testChoiceRemoval() {
    $ids = \Drupal::entityQuery('poll_choice')
      ->accessCheck(TRUE)
      ->condition('choice', $this->poll->choice[0]->entity->label())
      ->execute();
    $this->assertEquals(count($ids), 1, 'Choice 1 exists in the database');

    // Record a vote for the second choice.
    $edit = [
      'choice' => $this->poll->choice[1]->target_id,
    ];
    $this->drupalGet('poll/' . $this->poll->id());
    $this->submitForm($edit, 'Vote');

    // Assert the selected option.
    $xml = $this->xpath('//dt[text()=:choice]/following-sibling::dd[1]/div', [':choice' => $this->poll->choice[1]->entity->label()]);
    $this->assertEquals(1, $xml[0]->getAttribute('data-value'));

    // Edit the poll, and try to delete first poll choice.
    $this->drupalGet("poll/" . $this->poll->id() . "/edit");
    $edit = ['choice[0][choice]' => ''];
    $this->submitForm($edit, 'Save');

    // Click on the poll title to go to poll page.
    $this->drupalGet('admin/content/poll');
    $this->clickLink($this->poll->label());

    // Check the first poll choice is deleted, while the others remain.
    $this->assertSession()->pageTextNotContains($this->poll->choice[0]->entity->label());
    $this->assertSession()->pageTextContains($this->poll->choice[1]->entity->label());
    $this->assertSession()->pageTextContains($this->poll->choice[2]->entity->label());

    $ids = \Drupal::entityQuery('poll_choice')
      ->accessCheck(TRUE)
      ->condition('choice', $this->poll->choice[0]->entity->label())
      ->execute();
    $this->assertEquals(count($ids), 0, 'Choice 1 has been deleted in the database');

    // Ensure that the existing vote still shows.
    $this->drupalGet('poll/' . $this->poll->id());
    $vote = $this->poll->choice[1]->target_id;
    $vote_recorded = \Drupal::database()->query('SELECT chid FROM {poll_vote} WHERE chid = :chid', [':chid' => $vote])->fetch();
    $this->assertFalse(empty($vote_recorded), 'Vote in Choice 2 still in the database');

    // Assert the selected option.
    $xml = $this->xpath('//dt[text()=:choice]/following-sibling::dd[1]/div', [':choice' => $this->poll->choice[1]->entity->label()]);
    $this->assertEquals(1, $xml[0]->getAttribute('data-value'));

    // Edit the poll, and try to delete first poll choice.
    $this->drupalGet("poll/" . $this->poll->id() . "/edit");
    $edit = [
      'choice[0][choice]' => '',
    ];
    $this->submitForm($edit, 'Save');

    // Click on the poll title to go to poll page.
    $this->drupalGet('admin/content/poll');
    $this->clickLink($this->poll->label());

    // Check the poll choice (which had a vote) is deleted.
    $elements = $this->xpath('//input[@value="Vote"]');
    $this->assertTrue(isset($elements[0]), "vote deleted successfully");

    // Assert that the existing vote has been deleted from the database.
    $vote_deleted = \Drupal::database()->query('SELECT chid FROM {poll_vote} WHERE chid = :chid', [':chid' => $vote])->fetch();
    $this->assertTrue(empty($vote_deleted), 'Vote in Choice 2 has been deleted from the database');
  }

}
