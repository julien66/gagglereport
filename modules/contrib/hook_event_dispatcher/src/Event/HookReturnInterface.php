<?php

declare(strict_types=1);

namespace Drupal\hook_event_dispatcher\Event;

/**
 * Defines an interface for events with return values.
 */
interface HookReturnInterface {

  /**
   * Get the return value to pass to the hook.
   *
   * @return mixed
   *   The return value.
   */
  public function getReturnValue();

}
