<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class EntityExtraFieldInfoEvent.
 */
#[HookEvent(id: 'entity_extra_field_info', hook: 'entity_extra_field_info')]
class EntityExtraFieldInfoEvent extends Event implements EventInterface, HookReturnInterface {

  /**
   * Field info.
   *
   * @var array
   */
  private array $fieldInfo = [];

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_EXTRA_FIELD_INFO;
  }

  /**
   * Get the field info.
   *
   * @return array
   *   Field info.
   */
  public function getFieldInfo(): array {
    return $this->fieldInfo;
  }

  /**
   * Set the field info.
   *
   * @param array $fieldInfo
   *   Field info.
   */
  public function setFieldInfo(array $fieldInfo): void {
    $this->fieldInfo = $fieldInfo;
  }

  /**
   * Add field info for a form display.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $fieldName
   *   The field name.
   * @param array $info
   *   The field info.
   */
  public function addDisplayFieldInfo(string $entityType, string $bundle, string $fieldName, array $info): void {
    $this->addFieldInfo($entityType, $bundle, $fieldName, $info, 'display');
  }

  /**
   * Add field info for a given type.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $fieldName
   *   The field name.
   * @param array $info
   *   The field info.
   * @param string $type
   *   The type.
   */
  private function addFieldInfo(string $entityType, string $bundle, string $fieldName, array $info, string $type): void {
    $this->fieldInfo[$entityType][$bundle][$type][$fieldName] = $info;
  }

  /**
   * Add field info for a form display.
   *
   * @param string $entityType
   *   The entity type.
   * @param string $bundle
   *   The bundle.
   * @param string $fieldName
   *   The field name.
   * @param array $info
   *   The field info.
   */
  public function addFormFieldInfo(string $entityType, string $bundle, string $fieldName, array $info): void {
    $this->addFieldInfo($entityType, $bundle, $fieldName, $info, 'form');
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getFieldInfo();
  }

}
