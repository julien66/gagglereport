<?php

namespace Drupal\core_event_dispatcher\Event\Theme;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\PageHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class PageBottomEvent.
 */
#[HookEvent(id: 'page_bottom', hook: 'page_bottom')]
class PageBottomEvent extends Event implements EventInterface {

  /**
   * The build array.
   *
   * @var array
   */
  private array $build = [];

  /**
   * PageBottomEvent constructor.
   *
   * @param array $build
   *   The build array.
   */
  public function __construct(array &$build) {
    $this->build = &$build;
  }

  /**
   * Get the build array.
   *
   * @return array
   *   The build array.
   */
  public function &getBuild(): array {
    return $this->build;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return PageHookEvents::PAGE_BOTTOM;
  }

}
