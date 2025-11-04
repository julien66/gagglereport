<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class AbstractEntityEvent.
 */
abstract class AbstractEntityEvent extends Event implements EventInterface {

  /**
   * AbstractEntityEvent constructor.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The Entity.
   */
  public function __construct(protected readonly EntityInterface $entity) {
  }

  /**
   * Get the Entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The Entity.
   */
  public function getEntity(): EntityInterface {
    return $this->entity;
  }

}
