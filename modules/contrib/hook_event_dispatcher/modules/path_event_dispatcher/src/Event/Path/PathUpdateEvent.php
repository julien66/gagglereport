<?php

namespace Drupal\path_event_dispatcher\Event\Path;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\path_event_dispatcher\PathHookEvents;

/**
 * Class UpdatePathEvent.
 */
#[HookEvent(id: 'path_update', hook: 'path_alias_update')]
final class PathUpdateEvent extends AbstractPathEvent {

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return PathHookEvents::PATH_UPDATE;
  }

}
