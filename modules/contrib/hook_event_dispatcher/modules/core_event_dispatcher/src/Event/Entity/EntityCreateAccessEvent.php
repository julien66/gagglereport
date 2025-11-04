<?php

declare(strict_types=1);

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Session\AccountInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\AccessEventInterface;
use Drupal\hook_event_dispatcher\Event\AccessEventTrait;
use Drupal\hook_event_dispatcher\Event\EventFactoryInterface;
use Drupal\hook_event_dispatcher\Event\EventFactoryTrait;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class EntityCreateAccessEvent.
 */
#[HookEvent(id: 'entity_create_access', hook: 'entity_create_access')]
final class EntityCreateAccessEvent extends Event implements EventInterface, EventFactoryInterface, AccessEventInterface, HookReturnInterface {

  use EventFactoryTrait;
  use AccessEventTrait;

  /**
   * EntityCreateAccessEvent constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account for which to check access.
   * @param array $context
   *   An associative array of additional context values.
   * @param string|null $entityBundle
   *   The entity bundle name.
   */
  public function __construct(AccountInterface $account, protected readonly array $context, protected readonly string|int|null $entityBundle = NULL) {
    $this->account = $account;
  }

  /**
   * Gets additional context values.
   *
   * @return array
   *   An array of additional context values.
   */
  public function getContext(): array {
    return $this->context;
  }

  /**
   * Gets the entity bundle name.
   *
   * @return string|null
   *   The entity bundle name.
   */
  public function getEntityBundle(): ?string {
    return $this->entityBundle ? (string) $this->entityBundle : NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_CREATE_ACCESS;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getAccessResult();
  }

}
