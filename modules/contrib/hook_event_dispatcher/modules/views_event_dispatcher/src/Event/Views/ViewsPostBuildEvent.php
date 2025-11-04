<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsPostBuildEvent.
 */
#[HookEvent(id: 'views_post_build', hook: 'views_post_build')]
class ViewsPostBuildEvent extends AbstractViewsEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_POST_BUILD;
  }

}
