<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class EntityTranslationInsertEvent.
 */
#[HookEvent(id: 'entity_translation_insert', hook: 'entity_translation_insert')]
class EntityTranslationInsertEvent extends AbstractEntityEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_TRANSLATION_INSERT;
  }

}
