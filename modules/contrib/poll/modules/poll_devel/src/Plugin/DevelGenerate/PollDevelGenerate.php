<?php

namespace Drupal\poll_devel\Plugin\DevelGenerate;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\devel_generate\DevelGenerateBase;
use Drupal\poll\Entity\Poll;
use Drupal\poll\Entity\PollChoice;
use Drupal\poll\PollVoteStorageInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a DevelGenerate plugin for poll entities.
 *
 * @DevelGenerate(
 *   id = "poll",
 *   label = @Translation("Polls"),
 *   description = @Translation("Generate Polls. Optionally generate votes and delete current polls"),
 *   url = "poll",
 *   permission = "administer devel_generate",
 *   settings = {
 *     "num" = 50,
 *     "kill" = FALSE,
 *   }
 * )
 */
class PollDevelGenerate extends DevelGenerateBase implements ContainerFactoryPluginInterface {

  /**
   * The poll Vote storage.
   *
   * @var \Drupal\poll\PollVoteStorageInterface
   */
  protected PollVoteStorageInterface $pollVoteStorage;

  /**
   * The time interface.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $timeInterface;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create(
      $container,
      $configuration,
      $plugin_id,
      $plugin_definition,
    );
    $instance->pollVoteStorage = $container->get('poll_vote.storage');
    $instance->timeInterface = $container->get('datetime.time');

    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state): array {
    $form['poll_nbr'] = [
      '#type' => 'number',
      '#title' => $this->t('How many polls would you like to generate?'),
      '#default_value' => 10,
      '#required' => TRUE,
      '#min' => 1,
      '#size' => 10,
    ];

    $form['vote_nbr'] = [
      '#type' => 'number',
      '#title' => $this->t('How many votes would you like to generate for each poll?'),
      '#default_value' => 25,
      '#min' => 0,
      '#size' => 10,
    ];

    $form['kill'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Delete all polls before generating new polls'),
      '#default_value' => FALSE,
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  protected function generateElements(array $values): void {
    // Delete first.
    if ($values['kill']) {
      $delete_count = 0;
      foreach (Poll::loadMultiple() as $poll) {
        $poll->delete();
        $delete_count++;
      }
      $this->setMessage($this->formatPlural($delete_count, 'Deleted 1 poll', '@count polls deleted'));
    }

    for ($i = 1; $i <= $values['poll_nbr']; $i++) {
      // Random choices.
      $choices = [];
      for ($ci = 1; $ci <= mt_rand(2, 10); $ci++) {
        $poll_choice = PollChoice::create([
          'choice' => $this->getRandom()->word(mt_rand(2, 50)),
        ]);
        $poll_choice->save();
        $choices[] = $poll_choice->id();
      }

      // Create a poll entity.
      $poll = Poll::create([
        'question' => $this->getRandom()->word(mt_rand(2, 50)),
        'choice' => $choices,
      ]);

      $poll->save();

      // It's possible we have 0 votes to generate.
      if ($values['vote_nbr']) {
        for ($vi = 1; $vi <= $values['vote_nbr']; $vi++) {
          // Get a random choice.
          $choice_id = $choices[mt_rand(0, (count($choices) - 1))];

          // Save a vote.
          $options = [];
          $options['chid'] = $choice_id;
          // Fake the UID to be the same as the vote index +1 so we skip user 1.
          $options['uid'] = $vi + 1;
          $options['pid'] = $poll->id();
          $options['hostname'] = '0.0.0.0';
          $options['timestamp'] = $this->timeInterface->getRequestTime();
          $this->pollVoteStorage->saveVote($options);
        }
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function validateDrushParams(array $args, array $options = []): array {
    return [
      'poll_nbr' => $options['poll_nbr'],
      'vote_nbr' => $options['vote_nbr'],
      'kill' => $options['kill'],
    ];
  }

}
