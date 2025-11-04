<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class EntityBundleInfoEvent.
 */
#[HookEvent(id: 'entity_bundle_info', hook: 'entity_bundle_info')]
class EntityBundleInfoEvent extends Event implements EventInterface, HookReturnInterface {

  private array $bundles = [];

  /**
   * EntityBundleInfoEvent constructor.
   */
  public function __construct() {
  }

  /**
   * @param string $entity_type_id
   *   The entity type id.
   * @param string $bundle
   *   The bundle name of the entity type.
   * @param array $info
   *   An associative array of bundle info with the following keys:
   *   - label: The human-readable name of the bundle.
   *   - uri_callback: (optional) The same as the 'uri_callback' key defined
   *   for
   *     the entity type in the EntityTypeManager, but for the bundle only.
   *   When
   *     determining the URI of an entity, if a 'uri_callback' is defined for
   *   both the entity type and the bundle, the one for the bundle is used.
   *   - translatable: (optional) A boolean value specifying whether this
   *   bundle
   *     has translation support enabled. Defaults to FALSE.
   *   - class: (optional) The fully qualified class name for this bundle. If
   *     omitted, the class from the entity type definition will be used.
   *   Multiple bundles must not use the same subclass. If a class is reused by
   *   multiple bundles, an
   *   \Drupal\Core\Entity\Exception\AmbiguousBundleClassException will be
   *   thrown.
   */
  public function addBundleInfo(string $entity_type_id, string $bundle, array $info): void {
    $this->bundles[$entity_type_id][$bundle] = $info;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->bundles;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_BUNDLE_INFO;
  }

}
