<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class EntityOperationAlterEvent.
 */
#[HookEvent(id: 'entity_operation_alter', alter: 'entity_operation')]
class EntityOperationAlterEvent extends Event implements EventInterface {

  /**
   * The operations.
   *
   * @var array
   */
  private array $operations = [];

  /**
   * EntityOperationAlterEvent constructor.
   *
   * @param array $operations
   *   The array of operations being altered.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   */
  public function __construct(array &$operations, private readonly EntityInterface $entity) {
    $this->operations = &$operations;
  }

  /**
   * Get the operations.
   *
   * Returned by reference to be modified.
   *
   * @return array
   *   The operations.
   */
  public function &getOperations(): array {
    return $this->operations;
  }

  /**
   * Get the entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The entity.
   */
  public function getEntity(): EntityInterface {
    return $this->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_OPERATION_ALTER;
  }

}
