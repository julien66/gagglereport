<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsDataAlterEvent.
 */
#[HookEvent(id: 'views_data_alter', alter: 'views_data')]
final class ViewsDataAlterEvent extends Event implements EventInterface {

  /**
   * Data.
   *
   * @var array
   */
  private array $data = [];

  /**
   * ViewsDataAlterEvent constructor.
   *
   * @param array $data
   *   Data.
   */
  public function __construct(array &$data) {
    $this->data = &$data;
  }

  /**
   * Get data by reference.
   *
   * @return array
   *   Data.
   */
  public function &getData(): array {
    return $this->data;
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_DATA_ALTER;
  }

}
