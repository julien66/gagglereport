<?php

namespace Drupal\user_event_dispatcher\Event\User;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Session\AccountInterface;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\user_event_dispatcher\UserHookEvents;

/**
 * Class UserCancelEvent.
 */
#[HookEvent(id: 'user_cancel', hook: 'user_cancel')]
final class UserCancelEvent extends Event implements EventInterface {

  /**
   * UserCancelEvent constructor.
   *
   * @param array $edit
   *   The array of form values submitted by the user.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Account.
   * @param string $method
   *   The account cancellation method.
   */
  public function __construct(private readonly array $edit, private readonly AccountInterface $account, private readonly string $method) {
  }

  /**
   * Get edit array.
   *
   * @return array
   *   The array of form values submitted by the user.
   */
  public function getEdit(): array {
    return $this->edit;
  }

  /**
   * Get the account.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   Account.
   */
  public function getAccount(): AccountInterface {
    return $this->account;
  }

  /**
   * Get method.
   *
   * @return string
   *   The account cancellation method.
   */
  public function getMethod(): string {
    return $this->method;
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return UserHookEvents::USER_CANCEL;
  }

}
