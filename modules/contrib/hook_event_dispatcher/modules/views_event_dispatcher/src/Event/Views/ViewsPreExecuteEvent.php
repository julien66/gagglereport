<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsPreExecuteEvent.
 */
#[HookEvent(id: 'views_pre_execute', hook: 'views_pre_execute')]
class ViewsPreExecuteEvent extends AbstractViewsEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_PRE_EXECUTE;
  }

}
