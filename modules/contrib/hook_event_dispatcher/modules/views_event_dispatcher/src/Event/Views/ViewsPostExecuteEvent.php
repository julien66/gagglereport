<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsPostExecuteEvent.
 */
#[HookEvent(id: 'views_post_execute', hook: 'views_post_execute')]
class ViewsPostExecuteEvent extends AbstractViewsEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_POST_EXECUTE;
  }

}
