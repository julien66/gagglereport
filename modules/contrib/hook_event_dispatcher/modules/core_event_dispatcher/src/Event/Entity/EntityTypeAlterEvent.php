<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class EntityTypeBuildEvent.
 */
#[HookEvent(id: 'entity_type_alter', alter: 'entity_type')]
class EntityTypeAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * Get the entity types.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
   *   Entity types info.
   */
  public function &getEntityTypes(): array {
    return $this->definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_TYPE_ALTER;
  }

}
