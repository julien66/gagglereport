<?php

declare(strict_types=1);

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class EntityBundleFieldInfoAlterEvent.
 */
#[HookEvent(id: 'entity_bundle_field_info_alter', alter: 'entity_bundle_field_info')]
class EntityBundleFieldInfoAlterEvent extends Event implements EventInterface {

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
   * @param string|int $bundle
   *   The bundle name.
   */
  public function __construct(array &$fields, private readonly EntityTypeInterface $entityType, private readonly string|int $bundle) {
    $this->fields = &$fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_BUNDLE_FIELD_INFO_ALTER;
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
   * Get the EntityType.
   *
   * @return \Drupal\Core\Entity\EntityTypeInterface
   *   The EntityType.
   */
  public function getEntityType(): EntityTypeInterface {
    return $this->entityType;
  }

  /**
   * Gets the Bundle.
   *
   * @return string
   *   The Bundle.
   */
  public function getBundle(): string {
    return (string) $this->bundle;
  }

}
