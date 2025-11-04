<?php

namespace Drupal\webform_event_dispatcher\Event\WebformElement;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;
use Drupal\webform_event_dispatcher\WebformHookEvents;

/**
 * Class WebformElementInfoAlterEvent.
 */
#[HookEvent(id: 'webform_element_info_alter', alter: 'webform_element_info')]
class WebformElementInfoAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return WebformHookEvents::WEBFORM_ELEMENT_INFO_ALTER;
  }

}
