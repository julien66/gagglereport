<?php

namespace Drupal\poll\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;
use Drupal\Core\Url;

/**
 * Provides a form for deleting a poll.
 */
class PollDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t('All associated votes will be deleted too. This action cannot be undone.');
  }

  /**
   * {@inheritdoc}
   */
  protected function getRedirectUrl() {
    return new Url('poll.poll_list');
  }

}
