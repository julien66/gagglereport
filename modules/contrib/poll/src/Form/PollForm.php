<?php

namespace Drupal\poll\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the poll edit forms.
 */
class PollForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $poll = $this->entity;

    if ($poll->isNew()) {
      $title = $this->t('Add new poll');
    }
    else {
      $title = $this->t('Edit @label', ['@label' => $poll->label()]);
    }
    $form['#title'] = $title;

    foreach ($form['choice']['widget'] as $key => $choice) {
      if (is_int($key) && $form['choice']['widget'][$key]['choice']['#default_value'] != NULL) {
        $form['choice']['widget'][$key]['choice']['#attributes'] = ['class' => ['poll-existing-choice']];
      }
    }

    $form['anonymous_vote_restriction']['#states'] = [
      'visible' => [
        'input[name="anonymous_vote_allow[value]"]' => ['checked' => TRUE],
      ],
      'required' => [
        'input[name="anonymous_vote_allow[value]"]' => ['checked' => TRUE],
      ],
    ];

    $form['#attached'] = ['library' => ['poll/admin']];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $poll = $this->buildEntity($form, $form_state);
    /** @var \Drupal\poll\PollStorage $poll_storage */
    $poll_storage = $this->entityTypeManager->getStorage('poll');
    // Check for duplicate titles.
    $result = $poll_storage->getPollDuplicates($poll);
    foreach ($result as $item) {
      if (strcasecmp($item->label(), $poll->label()) == 0) {
        $form_state->setErrorByName('question', $this->t('A poll named %poll already exists. Enter a unique question.', ['%poll' => $poll->label()]));
      }
    }
    return parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $poll = $this->entity;
    $insert = (bool) $poll->id();
    $status = $poll->save();
    if ($insert) {
      $this->messenger()->addMessage($this->t('The poll %poll has been updated.', ['%poll' => $poll->label()]));
    }
    else {
      $this->logger('poll')->notice('Poll %poll added.', [
        '%poll' => $poll->label(),
        'link' => $poll->toLink()->toString(),
      ]);
      $this->messenger()->addMessage($this->t('The poll %poll has been added.', ['%poll' => $poll->label()]));
    }

    if ($poll->id()) {
      $form_state->setRedirect(
        'entity.poll.canonical',
        ['poll' => $poll->id()]
      );
    }

    return $status;
  }

}
