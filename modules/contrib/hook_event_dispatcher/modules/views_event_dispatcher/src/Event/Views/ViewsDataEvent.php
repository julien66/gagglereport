<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;
use Drupal\views_event_dispatcher\ViewsHookEvents;
use function array_merge_recursive;

/**
 * Class ViewsDataEvent.
 */
#[HookEvent(id: 'views_data', hook: 'views_data')]
final class ViewsDataEvent extends Event implements EventInterface, HookReturnInterface {

  /**
   * New views data.
   *
   * @var array
   */
  private array $data = [];

  /**
   * Add data to the views data.
   *
   * @param array $data
   *   Data to add to the views data.
   *
   * @see \hook_views_data()
   */
  public function addData(array $data): void {
    $this->data = array_merge_recursive($this->data, $data);
  }

  /**
   * Get data.
   *
   * @return array
   *   Data.
   */
  public function getData(): array {
    return $this->data;
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_DATA;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getData();
  }

}
