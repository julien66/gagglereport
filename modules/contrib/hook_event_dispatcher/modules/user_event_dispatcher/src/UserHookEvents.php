<?php

namespace Drupal\user_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for user hooks.
 */
final class UserHookEvents {

  /**
   * Act on user account cancellations.
   *
   * @Event
   *
   * @see \Drupal\user_event_dispatcher\Event\User\UserCancelEvent
   * @see user_event_dispatcher_user_cancel()
   * @see hook_user_cancel()
   *
   * @var string
   */
  public const USER_CANCEL = HookEventDispatcherInterface::PREFIX . 'user.cancel';

  /**
   * Modify account cancellation methods.
   *
   * @Event
   *
   * @see \Drupal\user_event_dispatcher\Event\User\UserCancelMethodsAlterEvent
   * @see user_event_dispatcher_user_cancel_methods_alter()
   * @see hook_user_cancel_methods_alter()
   *
   * @var string
   */
  public const USER_CANCEL_METHODS_ALTER = HookEventDispatcherInterface::PREFIX . 'user.cancel_methods_alter';

  /**
   * Alter the username that is displayed for a user.
   *
   * @Event
   *
   * @see \Drupal\user_event_dispatcher\Event\User\UserFormatNameAlterEvent
   * @see user_event_dispatcher_user_format_name_alter()
   * @see hook_user_format_name_alter()
   *
   * @var string
   */
  public const USER_FORMAT_NAME_ALTER = HookEventDispatcherInterface::PREFIX . 'user.format_name_alter';

  /**
   * The user just logged in.
   *
   * @Event
   *
   * @see \Drupal\user_event_dispatcher\Event\User\UserLoginEvent
   * @see user_event_dispatcher_user_login()
   * @see hook_user_login()
   *
   * @var string
   */
  public const USER_LOGIN = HookEventDispatcherInterface::PREFIX . 'user.login';

  /**
   * The user just logged out.
   *
   * @Event
   *
   * @see \Drupal\user_event_dispatcher\Event\User\UserLogoutEvent
   * @see user_event_dispatcher_user_logout()
   * @see hook_user_logout()
   *
   * @var string
   */
  public const USER_LOGOUT = HookEventDispatcherInterface::PREFIX . 'user.logout';

}
