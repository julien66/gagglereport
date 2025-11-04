<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class EntityDeleteEvent.
 */
#[HookEvent(id: 'entity_delete', hook: 'entity_delete')]
class EntityDeleteEvent extends AbstractEntityEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_DELETE;
  }

}
