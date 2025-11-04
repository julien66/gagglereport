<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\views\ViewExecutable;

/**
 * Class AbstractViewsEvent.
 */
abstract class AbstractViewsEvent extends Event implements EventInterface {

  /**
   * AbstractViewsEvent constructor.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   The view.
   */
  public function __construct(private readonly ViewExecutable $view) {
  }

  /**
   * Get the view.
   *
   * @return \Drupal\views\ViewExecutable
   *   The view.
   */
  public function getView(): ViewExecutable {
    return $this->view;
  }

}
