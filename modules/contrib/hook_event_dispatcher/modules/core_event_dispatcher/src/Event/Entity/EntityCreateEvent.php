<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class EntityCreateEvent.
 */
#[HookEvent(id: 'entity_create', hook: 'entity_create')]
class EntityCreateEvent extends AbstractEntityEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_CREATE;
  }

}
