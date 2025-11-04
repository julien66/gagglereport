<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class EntityBundleDeleteEvent.
 */
#[HookEvent(id: 'entity_bundle_delete', hook: 'entity_bundle_delete')]
class EntityBundleDeleteEvent extends EntityBundleEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_BUNDLE_DELETE;
  }

}
