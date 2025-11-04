<?php

namespace Drupal\core_event_dispatcher\Event\Menu;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\core_event_dispatcher\MenuHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class MenuLocalTasksAlterEvent.
 */
#[HookEvent(id: 'menu_local_tasks_alter', alter: 'menu_local_tasks')]
class MenuLocalTasksAlterEvent extends Event implements EventInterface {

  /**
   * An associative array of menu local tasks data.
   *
   * @var array
   */
  protected array $data = [];

  /**
   * MenuLocalTaskAlterEvent constructor.
   *
   * @param array $data
   *   An associative array containing list of (up to 2) tab levels that
   *   contain a list of tabs keyed by their href, each one being an
   *   associative array.
   * @param string $routeName
   *   The route name of the page.
   * @param \Drupal\Core\Cache\RefinableCacheableDependencyInterface $cacheability
   *   The cacheability metadata for the current route's local tasks.
   */
  public function __construct(array &$data, protected readonly string $routeName, protected readonly RefinableCacheableDependencyInterface $cacheability) {
    $this->data = &$data;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return MenuHookEvents::MENU_LOCAL_TASKS_ALTER;
  }

  /**
   * Gets the menu local tasks data.
   *
   * @return array
   *   An associative array containing list of (up to 2) tab levels.
   */
  public function &getData(): array {
    return $this->data;
  }

  /**
   * Sets the menu local tasks data.
   *
   * @param array $data
   *   The menu local tasks data.
   */
  public function setData(array $data): void {
    $this->data = $data;
  }

  /**
   * Gets the route name of the page.
   *
   * @return string
   *   The route name of the page.
   */
  public function getRouteName(): string {
    return $this->routeName;
  }

  /**
   * Gets the cacheability metadata for the current route's local tasks.
   *
   * @return \Drupal\Core\Cache\RefinableCacheableDependencyInterface
   *   The cacheability metadata.
   */
  public function getCacheability(): RefinableCacheableDependencyInterface {
    return $this->cacheability;
  }

}
