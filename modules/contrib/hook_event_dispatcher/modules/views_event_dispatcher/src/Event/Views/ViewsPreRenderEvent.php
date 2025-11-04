<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsPreRenderEvent.
 */
#[HookEvent(id: 'views_pre_render', hook: 'views_pre_render')]
class ViewsPreRenderEvent extends AbstractViewsEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_PRE_RENDER;
  }

}
