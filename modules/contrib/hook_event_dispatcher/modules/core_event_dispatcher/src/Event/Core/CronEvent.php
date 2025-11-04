<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class CronEvent.
 */
#[HookEvent(id: 'cron', hook: 'cron')]
final class CronEvent extends Event implements EventInterface {

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::CRON;
  }

}
