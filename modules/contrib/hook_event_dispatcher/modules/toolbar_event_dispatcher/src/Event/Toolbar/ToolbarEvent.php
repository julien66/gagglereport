<?php

namespace Drupal\toolbar_event_dispatcher\Event\Toolbar;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\toolbar_event_dispatcher\ToolbarHookEvents;

/**
 * Class ToolbarEvent.
 */
#[HookEvent(id: 'toolbar', hook: 'toolbar')]
final class ToolbarEvent extends Event implements EventInterface {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ToolbarHookEvents::TOOLBAR;
  }

}
