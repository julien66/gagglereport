<?php

declare(strict_types=1);

namespace Drupal\hook_event_dispatcher\Event;

/**
 * Defines an interface events which can constructs instances of themselves.
 *
 * @todo merge this into EventInterface once every event extends
 * HookEventDispatcherEventBase or implements this interface.
 */
interface EventFactoryInterface {

  /**
   * Constructs an instance of this event class.
   *
   * @param mixed ...$args
   *   Arguments from the Module Handler.
   *
   * @return static
   *   An instance of this class.
   */
  public static function create(&...$args): EventFactoryInterface;

}
