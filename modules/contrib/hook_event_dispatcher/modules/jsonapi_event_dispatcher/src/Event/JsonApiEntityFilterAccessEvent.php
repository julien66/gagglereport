<?php

namespace Drupal\jsonapi_event_dispatcher\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;
use Drupal\jsonapi_event_dispatcher\JsonApiHookEvents;

/**
 * Class JsonapiEntityFilterAccessEvent.
 */
#[HookEvent(id: 'json_api_entity_filter_access', hook: 'jsonapi_entity_filter_access')]
class JsonApiEntityFilterAccessEvent extends Event implements EventInterface, HookReturnInterface {

  /**
   * An array keyed by a constant which identifies a subset of entities.
   *
   * @var \Drupal\Core\Access\AccessResultInterface[]
   */
  protected $accessResults = [];

  /**
   * JsonapiEntityFilterAccessEvent constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entityType
   *   The entity type of the entity to be filtered upon.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account for which to check access.
   */
  public function __construct(protected readonly EntityTypeInterface $entityType, protected readonly AccountInterface $account) {
  }

  /**
   * Gets the entity type of the entity to be filtered upon.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   The entity type of the entity to be filtered upon.
   */
  public function getEntityType(): EntityTypeInterface {
    return $this->entityType;
  }

  /**
   * Gets the account for which to check access.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   The account for which to check access.
   */
  public function getAccount(): AccountInterface {
    return $this->account;
  }

  /**
   * Gets the access results.
   *
   * @return \Drupal\Core\Access\AccessResultInterface[]
   *   An array keyed by a constant which identifies a subset of entities.
   */
  public function getAccessResults(): array {
    return $this->accessResults;
  }

  /**
   * Adds an access result to the subset.
   *
   * @param string $subset
   *   The subset of entities.
   * @param \Drupal\Core\Access\AccessResultInterface $accessResult
   *   The access result.
   */
  public function addAccessResult(string $subset, AccessResultInterface $accessResult): void {
    if (empty($this->accessResults[$subset])) {
      $this->accessResults[$subset] = AccessResult::neutral();
    }

    $this->accessResults[$subset] = $this->accessResults[$subset]->orIf($accessResult);
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return JsonApiHookEvents::JSONAPI_ENTITY_FILTER_ACCESS;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getAccessResults();
  }

}
