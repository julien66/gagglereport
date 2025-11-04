<?php

namespace Drupal\toolbar_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for toolbar hooks.
 */
final class ToolbarHookEvents {

  /**
   * Alter the toolbar menu after hook_toolbar() is invoked.
   *
   * @Event
   *
   * @see \Drupal\toolbar_event_dispatcher\Event\Toolbar\ToolbarEvent
   * @see toolbar_event_dispatcher_toolbar()
   * @see hook_toolbar()
   *
   * @var string
   */
  public const TOOLBAR = HookEventDispatcherInterface::PREFIX . 'toolbar';

  /**
   * Alter the toolbar menu after hook_toolbar() is invoked.
   *
   * @Event
   *
   * @see \Drupal\toolbar_event_dispatcher\Event\Toolbar\ToolbarAlterEvent
   * @see toolbar_event_dispatcher_toolbar_alter()
   * @see hook_toolbar_alter()
   *
   * @var string
   */
  public const TOOLBAR_ALTER = HookEventDispatcherInterface::PREFIX . 'toolbar.alter';

}
