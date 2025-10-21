<?php

namespace Drupal\Tests\poll\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\poll\Entity\Poll;
use Drupal\poll\Entity\PollChoice;
use Drupal\poll\Form\PollViewForm;
use Drupal\poll\PollInterface;

/**
 * Base class for Poll Kernel tests.
 */
abstract class PollKernelTestBase extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['options', 'poll', 'user'];

  /**
   * The poll view form.
   *
   * @var \Drupal\poll\Form\PollViewForm
   */
  protected PollViewForm $pollViewForm;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setup();
    $this->installSchema('poll', 'poll_vote');
    $this->installEntitySchema('poll');
    $this->installEntitySchema('poll_choice');
    $this->pollViewForm = PollViewForm::create($this->container);
  }

  /**
   * Creates and saves a poll.
   *
   * @param int $choice_count
   *   (optional) The number of choices to generate. Defaults to 7.
   *
   * @return \Drupal\poll\PollInterface
   *   A poll.
   */
  public function createPoll($choice_count = 2) {
    /** @var \Drupal\poll\PollInterface $poll */
    $poll = Poll::create([
      'question' => $this->randomMachineName(),
    ]);
    $poll_choice_ids = [];
    for ($i = 1; $i <= $choice_count; $i++) {
      $poll_choice = PollChoice::create([
        'choice' => $this->randomMachineName(),
      ]);
      $poll_choice->save();
      $poll_choice_ids[] = $poll_choice->id();
    }
    $poll
      ->set('anonymous_vote_allow', TRUE)
      ->set('choice', $poll_choice_ids)
      ->save();

    return $poll;
  }

  /**
   * Saves a vote for a given poll.
   */
  public function saveVote(PollInterface $poll, $choice_id) {
    $options = [];
    $options['chid'] = $choice_id;
    $options['uid'] = \Drupal::currentUser()->id();
    $options['pid'] = $poll->id();
    $options['hostname'] = \Drupal::request()->getClientIp();
    $options['timestamp'] = \Drupal::time()->getRequestTime();
    /** @var \Drupal\poll\PollVoteStorage $vote_storage */
    $vote_storage = \Drupal::service('poll_vote.storage');
    $vote_storage->saveVote($options);
  }

}
