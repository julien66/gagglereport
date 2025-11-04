<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventFactoryInterface;
use Drupal\hook_event_dispatcher\Event\EventFactoryTrait;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class EntityLoadEvent.
 */
#[HookEvent(id: 'entity_operation', hook: 'entity_operation')]
final class EntityOperationEvent extends Event implements EventInterface, EventFactoryInterface, HookReturnInterface {

  use EventFactoryTrait;

  /**
   * The operations.
   *
   * @var array
   */
  private array $operations = [];

  /**
   * EntityOperationEvent constructor.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   */
  public function __construct(private readonly EntityInterface $entity) {
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
   * Get the operations.
   *
   * @return array
   *   The operations.
   */
  public function getOperations(): array {
    return $this->operations;
  }

  /**
   * Set the operations.
   *
   * @param array $operations
   *   An array of operations.
   */
  public function setOperations(array $operations): void {
    $this->operations = $operations;
  }

  /**
   * Add an operation.
   *
   * @param string $name
   *   Operation name.
   * @param array $operation
   *   Operation definition.
   */
  public function addOperation(string $name, array $operation): void {
    $this->operations[$name] = $operation;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_OPERATION;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getOperations();
  }

}
