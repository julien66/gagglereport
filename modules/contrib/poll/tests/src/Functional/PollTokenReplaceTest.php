<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\poll\Entity\Poll;

/**
 * Tests poll token replacements in strings.
 *
 * @group poll
 */
class PollTokenReplaceTest extends PollTestBase {

  use StringTranslationTrait;

  /**
   * Creates a poll, then tests the tokens generated from it.
   */
  public function testPollTokenReplacement() {
    // Create a poll with three choices.
    $poll = $this->pollCreate(3);
    $poll->save();
    $poll_nid = $poll->id();

    // Create four users and have each of them vote.
    $vote_user1 = $this->drupalCreateUser(['access polls', 'access content']);
    $this->drupalLogin($vote_user1);
    $edit = [
      'choice' => (string) $this->getChoiceId($poll, 1),
    ];
    $this->drupalGet('poll/' . $poll_nid);
    $this->submitForm($edit, 'Vote');
    $this->drupalLogout();

    $vote_user2 = $this->drupalCreateUser(['access polls', 'access content']);
    $this->drupalLogin($vote_user2);
    $edit = [
      'choice' => (string) $this->getChoiceId($poll, 1),
    ];
    $this->drupalGet('poll/' . $poll_nid);
    $this->submitForm($edit, 'Vote');
    $this->drupalLogout();

    $vote_user3 = $this->drupalCreateUser(['access polls', 'access content']);
    $this->drupalLogin($vote_user3);
    $edit = [
      'choice' => (string) $this->getChoiceId($poll, 2),
    ];
    $this->drupalGet('poll/' . $poll_nid);
    $this->submitForm($edit, 'Vote');
    $this->drupalLogout();

    $vote_user4 = $this->drupalCreateUser(['access polls', 'access content']);
    $this->drupalLogin($vote_user4);
    $edit = [
      'choice' => (string) $this->getChoiceId($poll, 3),
    ];
    $this->drupalGet('poll/' . $poll_nid);
    $this->submitForm($edit, 'Vote');
    $this->drupalLogout();

    /** @var \Drupal\poll\Entity\Poll $poll */
    $poll = Poll::load($poll_nid);

    // Generate and test sanitized tokens.
    $tests = [];
    $tests['[poll:votes]'] = 4;
    $tests['[poll:winner]'] = $poll->getOptions()[$this->getChoiceId($poll, 1)];
    $tests['[poll:winner-votes]'] = 2;
    $tests['[poll:winner-percent]'] = 50;
    $tests['[poll:duration]'] = \Drupal::service('date.formatter')->formatInterval($poll->getRuntime());

    // Test to make sure that we generated something for each token.
    $this->assertFalse(in_array(0, array_map('strlen', $tests)), 'No empty tokens generated.');

    $token = \Drupal::service('token');
    foreach ($tests as $input => $expected) {
      $output = $token->replace($input, ['poll' => $poll]);
      $this->assertEquals($output, $expected, "Sanitized poll token $input replaced.");
    }

    // Generate and test unsanitized tokens.
    $tests['[poll:winner]'] = $poll->getOptions()[$this->getChoiceId($poll, 1)];

    foreach ($tests as $input => $expected) {
      $output = $token->replace($input, ['poll' => $poll], ['sanitize' => FALSE]);
      $this->assertEquals($output, $expected, "Unsanitized poll token $input replaced.");
    }
  }

}
