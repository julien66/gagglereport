<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Check that HTML special characters are displayed correctly.
 *
 * @group poll
 */
class PollHtmlSpecialCharactersTest extends PollTestBase {

  use StringTranslationTrait;

  /**
   * Special choice string.
   *
   * @var string
   */
  protected string $specialChoice;

  /**
   * Page title string.
   *
   * @var string
   */
  protected string $pageTitle;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->specialChoice = '> old & spice <';
    $this->pageTitle = 'Salt & pepper > here';

    $this->poll
      ->setQuestion($this->pageTitle)
      ->setAnonymousVoteAllow(TRUE)
      ->save();
  }

  /**
   * Test that HTML characters in the title are displayed correctly.
   */
  public function testPollQuestion() {
    // Verify user can view poll.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->statusCodeEquals(200);

    // Verify the page title.
    $result = $this->xpath("//div[contains(concat(' ', @class, ' '), ' block-page-title-block ')]/h1");
    $this->assertEquals($this->pageTitle, $result[0]->getText(), 'HTML entities displayed correctly in page title.');
    $this->drupalGet('poll/' . $this->poll->id());

    // Verify the poll title is escaped correctly in the poll results.
    $this->submitForm(['choice' => '1'], 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->drupalGet('polls');
    $this->assertSession()->elementTextContains('css', 'h3.poll-question', $this->pageTitle);
  }

  /**
   * Test that HTML characters in choices are displayed correctly.
   */
  public function testPollChoice() {
    $poll = $this->poll;
    // Update the first choice.
    $poll->choice[0]->entity->setChoice($this->specialChoice);
    $poll->choice[0]->entity->save();

    // View the updated poll.
    $this->drupalGet('poll/' . $this->poll->id());
    $this->assertSession()->statusCodeEquals(200);

    // Verify the updated choice.
    $result = $this->xpath("//div[@id='edit-choice']/div[1]/label/text()");
    $this->assertEquals($this->specialChoice, $result[0]->getText(), 'HTML entities displayed correctly in choice option.');
    $this->drupalGet('poll/' . $this->poll->id());

    // Vote.
    $this->submitForm(['choice' => '1'], 'Vote');
    $this->assertSession()->pageTextContains('Your vote has been recorded.');
    $this->assertSession()->pageTextContains('Total votes: 1');

    // Verify the results page.
    $result = $this->xpath('//*[@id="poll-view-form-1"]/div/dl/dt[1]/text()');
    $this->assertEquals($this->specialChoice, $result[0]->getText(), 'HTML entities displayed correctly in vote results.');
  }

}
