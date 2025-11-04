<?php

namespace Drupal\user_event_dispatcher\Event\User;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\user_event_dispatcher\UserHookEvents;

/**
 * Class UserCancelMethodsAlterEvent.
 */
#[HookEvent(id: 'user_cancel_methods_alter', alter: 'user_cancel_methods')]
final class UserCancelMethodsAlterEvent extends Event implements EventInterface {

  /**
   * Array containing user account cancellation methods, keyed by method id.
   *
   * @var array
   */
  private array $methods = [];

  /**
   * UserCancelMethodsAlterEvent constructor.
   *
   * @param array $methods
   *   Array containing user account cancellation methods, keyed by method id.
   */
  public function __construct(array &$methods) {
    $this->methods = &$methods;
  }

  /**
   * Get methods by reference.
   *
   * @return array
   *   Methods.
   */
  public function &getMethods(): array {
    return $this->methods;
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return UserHookEvents::USER_CANCEL_METHODS_ALTER;
  }

}
