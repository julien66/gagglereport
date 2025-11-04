<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class EntityTranslationDeleteEvent.
 */
#[HookEvent(id: 'entity_translation_delete', hook: 'entity_translation_delete')]
class EntityTranslationDeleteEvent extends AbstractEntityEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_TRANSLATION_DELETE;
  }

}
