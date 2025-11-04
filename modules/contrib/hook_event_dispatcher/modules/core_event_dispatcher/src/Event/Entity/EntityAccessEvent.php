<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Core\Access\AccessResultNeutral;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\AccessEventInterface;
use Drupal\hook_event_dispatcher\Event\AccessEventTrait;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class EntityAccessEvent.
 */
#[HookEvent(id: 'entity_access', hook: 'entity_access')]
class EntityAccessEvent extends AbstractEntityEvent implements AccessEventInterface, HookReturnInterface {

  use AccessEventTrait;

  /**
   * EntityAccessEvent constructor.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check access to.
   * @param string $operation
   *   The operation that is to be performed on $entity.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account trying to access the entity.
   */
  public function __construct(EntityInterface $entity, string $operation, AccountInterface $account) {
    parent::__construct($entity);

    $this->operation = $operation;
    $this->account = $account;
    $this->accessResult = new AccessResultNeutral();
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_ACCESS;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getAccessResult();
  }

}
