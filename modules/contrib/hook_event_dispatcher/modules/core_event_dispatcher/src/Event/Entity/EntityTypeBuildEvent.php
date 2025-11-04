<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class EntityTypeBuildEvent.
 */
#[HookEvent(id: 'entity_type_build', hook: 'entity_type_build')]
class EntityTypeBuildEvent extends Event implements EventInterface {

  /**
   * Field info.
   *
   * @var array
   */
  private array $entityTypes = [];

  /**
   * EntityTypeBuildEvent constructor.
   *
   * @param array $entityTypes
   *   Extra field info.
   */
  public function __construct(array &$entityTypes) {
    $this->entityTypes = &$entityTypes;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_TYPE_BUILD;
  }

  /**
   * Get the entity types.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface[]
   *   Entity types info.
   */
  public function &getEntityTypes(): array {
    return $this->entityTypes;
  }

}
