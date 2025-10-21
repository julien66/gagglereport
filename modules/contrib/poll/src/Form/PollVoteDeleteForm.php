<?php

namespace Drupal\poll\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\poll\PollVoteStorageInterface;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a form for deleting a vote.
 */
class PollVoteDeleteForm extends ContentEntityConfirmFormBase {

  /**
   * The poll vote storage service.
   *
   * @var \Drupal\poll\PollVoteStorageInterface
   */
  protected PollVoteStorageInterface $pollVoteStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->pollVoteStorage = $container->get('poll_vote.storage');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete this vote for %poll', ['%poll' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return $this->entity->toUrl();
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $uid = $this->getRequest()->attributes->get('user');
    $account = User::load($uid);
    $this->pollVoteStorage->cancelVote($this->entity, $account);
    $this->logger('poll')->notice('%user\'s vote in Poll #%poll deleted.', [
      '%user' => $account->id(),
      '%poll' => $this->entity->id(),
    ]);
    $this->messenger()->addMessage($this->t('Your vote was cancelled.'));

    // Display the original poll.
    $form_state->setRedirect('entity.poll.canonical', ['poll' => $this->entity->id()]);
  }

}
