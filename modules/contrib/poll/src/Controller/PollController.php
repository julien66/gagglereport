<?php

namespace Drupal\poll\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\poll\PollInterface;

/**
 * Returns responses for poll module routes.
 */
class PollController extends ControllerBase {

  /**
   * Route title callback.
   *
   * @param \Drupal\poll\PollInterface $poll
   *   The poll entity.
   *
   * @return string
   *   The poll label.
   */
  public function pollTitle(PollInterface $poll) {
    return $poll->label();
  }

}
