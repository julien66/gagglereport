<?php

namespace Drupal\path_event_dispatcher\Event\Path;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\path_event_dispatcher\PathHookEvents;

/**
 * Class PathInsertEvent.
 */
#[HookEvent(id: 'path_insert', hook: 'path_alias_insert')]
final class PathInsertEvent extends AbstractPathEvent {

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return PathHookEvents::PATH_INSERT;
  }

}
