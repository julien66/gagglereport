<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventFactoryInterface;
use Drupal\hook_event_dispatcher\Event\EventFactoryTrait;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class EntityBaseFieldInfoEvent.
 */
#[HookEvent(id: 'entity_base_field_info', hook: 'entity_base_field_info')]
final class EntityBaseFieldInfoEvent extends Event implements EventInterface, EventFactoryInterface, HookReturnInterface {

  use EventFactoryTrait;

  /**
   * The fields.
   *
   * @var array
   */
  private array $fields = [];

  /**
   * EntityBaseFieldInfoEvent constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entityType
   *   The entity type.
   */
  public function __construct(private readonly EntityTypeInterface $entityType) {
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_BASE_FIELD_INFO;
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

  /**
   * Get the fields.
   *
   * @return array
   *   The fields.
   */
  public function getFields(): array {
    return $this->fields;
  }

  /**
   * Set the fields.
   *
   * @param array $fields
   *   The fields.
   */
  public function setFields(array $fields): void {
    $this->fields = $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getFields();
  }

}
