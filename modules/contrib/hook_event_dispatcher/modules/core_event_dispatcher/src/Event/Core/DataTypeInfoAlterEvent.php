<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class DataTypeInfoAlterEvent.
 */
#[HookEvent(id: 'data_type_info_alter', alter: 'data_type_info')]
class DataTypeInfoAlterEvent extends Event implements EventInterface {

  /**
   * An array of data type information.
   *
   * @var array
   */
  private array $dataTypes = [];

  /**
   * DataTypeInfoAlterEvent constructor.
   *
   * @param array $dataTypes
   *   An array of data type information.
   */
  public function __construct(array &$dataTypes) {
    $this->dataTypes = &$dataTypes;
  }

  /**
   * Get data types info.
   *
   * @return array
   *   Data types info.
   */
  public function &getDataTypes(): array {
    return $this->dataTypes;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::DATA_TYPE_INFO_ALTER;
  }

}
