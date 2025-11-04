<?php

namespace Drupal\field_event_dispatcher\Event\Field;

use Drupal\Component\EventDispatcher\Event;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class FieldFormatterInfoAlterEvent.
 */
#[HookEvent(id: 'field_formatter_info_alter', alter: 'field_formatter_info')]
class FieldFormatterInfoAlterEvent extends Event implements EventInterface {

  /**
   * An array of information on existing field formatter types.
   *
   * @var array
   */
  private array $info = [];

  /**
   * FieldFormatterInfoAlterEvent constructor.
   *
   * @param array &$info
   *   An array of information on existing field formatter types.
   */
  public function __construct(array &$info) {
    $this->info = &$info;
  }

  /**
   * Get the existing field formatter type definitions.
   *
   * @return array
   *   An array of information on existing field formatter types.
   */
  public function &getInfo(): array {
    return $this->info;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FieldHookEvents::FIELD_FORMATTER_INFO_ALTER;
  }

}
