<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class LayoutAlterEvent.
 */
#[HookEvent(id: 'layout_alter', alter: 'layout')]
class LayoutAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::LAYOUT_ALTER;
  }

}
