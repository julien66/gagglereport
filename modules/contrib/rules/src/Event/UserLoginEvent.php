<?php

namespace Drupal\rules\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\user\UserInterface;

/**
 * Event that is fired when a user logs in.
 *
 * @see rules_user_login()
 */
class UserLoginEvent extends Event {

  const EVENT_NAME = 'rules_user_login';

  /**
   * The user account.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * Constructs the object.
   *
   * @param \Drupal\user\UserInterface $account
   *   The account of the user logged in.
   */
  public function __construct(UserInterface $account) {
    $this->account = $account;
  }

}
