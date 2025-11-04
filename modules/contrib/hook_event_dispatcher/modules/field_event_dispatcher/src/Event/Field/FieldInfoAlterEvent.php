<?php

namespace Drupal\field_event_dispatcher\Event\Field;

use Drupal\Component\EventDispatcher\Event;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Provides event to alter field info.
 */
#[HookEvent(id: 'field_info_alter', alter: 'field_info')]
class FieldInfoAlterEvent extends Event implements EventInterface {

  /**
   * An array of information on existing field types.
   *
   * @var array
   */
  private array $info = [];

  /**
   * FieldInfoAlterEvent constructor.
   *
   * @param array &$info
   *   An array of information on existing field types.
   */
  public function __construct(array &$info) {
    $this->info = &$info;
  }

  /**
   * Get the existing field type definitions.
   *
   * @return array
   *   An array of information on existing field types.
   */
  public function &getInfo(): array {
    return $this->info;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FieldHookEvents::FIELD_INFO_ALTER;
  }

}
