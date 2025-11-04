<?php

namespace Drupal\path_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for path hooks.
 */
final class PathHookEvents {

  /**
   * Respond to a path being inserted.
   *
   * @Event
   *
   * @see \Drupal\path_event_dispatcher\Event\Path\PathInsertEvent
   * @see path_event_dispatcher_path_alias_insert()
   *
   * @var string
   */
  public const PATH_INSERT = HookEventDispatcherInterface::PREFIX . 'path.insert';

  /**
   * Respond to a path being updated.
   *
   * @Event
   *
   * @see \Drupal\path_event_dispatcher\Event\Path\PathUpdateEvent
   * @see path_event_dispatcher_path_alias_update()
   *
   * @var string
   */
  public const PATH_UPDATE = HookEventDispatcherInterface::PREFIX . 'path.update';

  /**
   * Respond to a path being deleted.
   *
   * @Event
   *
   * @see \Drupal\path_event_dispatcher\Event\Path\PathDeleteEvent
   * @see path_event_dispatcher_path_alias_delete()
   *
   * @var string
   */
  public const PATH_DELETE = HookEventDispatcherInterface::PREFIX . 'path.delete';

}
