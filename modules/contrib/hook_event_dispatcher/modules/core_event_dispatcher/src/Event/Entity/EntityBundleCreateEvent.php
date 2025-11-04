<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class EntityBundleCreateEvent.
 */
#[HookEvent(id: 'entity_bundle_create', hook: 'entity_bundle_create')]
class EntityBundleCreateEvent extends EntityBundleEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_BUNDLE_CREATE;
  }

}
