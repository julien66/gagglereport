<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class EntityBundleInfoAlterEvent.
 */
#[HookEvent(id: 'entity_bundle_info_alter', alter: 'entity_bundle_info')]
class EntityBundleInfoAlterEvent extends Event implements EventInterface {

  /**
   * EntityBundleInfoAlterEvent constructor.
   *
   * @param array $bundles
   *   An array of bundles, keyed first by entity type, then by bundle name.
   */
  public function __construct(protected array &$bundles) {
  }

  /**
   * Gets the bundles info, keyed first by entity type, then by bundle name.
   *
   * @return array
   */
  public function &getBundles(): array {
    return $this->bundles;
  }

  /**
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle
   *   The bundle name of the entity type.
   * @param string $property
   *   The bundle info property.
   * @param mixed $value
   *   The bundle info property's value.
   */
  public function alterBundleInfo(string $entity_type_id, string $bundle, string $property, mixed $value): void {
    $this->bundles[$entity_type_id][$bundle][$property] = $value;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_BUNDLE_INFO_ALTER;
  }

}
