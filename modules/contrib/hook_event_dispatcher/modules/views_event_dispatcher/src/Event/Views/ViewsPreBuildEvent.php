<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsPreBuildEvent.
 */
#[HookEvent(id: 'views_pre_build', hook: 'views_pre_build')]
class ViewsPreBuildEvent extends AbstractViewsEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_PRE_BUILD;
  }

}
