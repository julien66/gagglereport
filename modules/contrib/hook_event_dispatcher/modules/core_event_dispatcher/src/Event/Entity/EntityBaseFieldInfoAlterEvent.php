<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class EntityBaseFieldInfoAlterEvent.
 */
#[HookEvent(id: 'entity_base_field_info_alter', alter: 'entity_base_field_info')]
class EntityBaseFieldInfoAlterEvent extends Event implements EventInterface {

  /**
   * Field info.
   *
   * @var array
   */
  private array $fields = [];

  /**
   * EntityExtraFieldInfoAlterEvent constructor.
   *
   * @param array $fields
   *   Extra field info.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entityType
   *   The entity type.
   */
  public function __construct(array &$fields, private readonly EntityTypeInterface $entityType) {
    $this->fields = &$fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_BASE_FIELD_INFO_ALTER;
  }

  /**
   * Get the field info.
   *
   * @return array
   *   Extra field info.
   */
  public function &getFields(): array {
    return $this->fields;
  }

  /**
   * Get the entity type.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   The entity type.
   */
  public function getEntityType(): EntityTypeInterface {
    return $this->entityType;
  }

}
