<?php

namespace Drupal\hook_event_dispatcher;

/**
 * Interface HookEventDispatcherInterface.
 */
interface HookEventDispatcherInterface {

  /**
   * Event name prefix to prevent name collision.
   */
  public const PREFIX = 'hook_event_dispatcher.';

}
