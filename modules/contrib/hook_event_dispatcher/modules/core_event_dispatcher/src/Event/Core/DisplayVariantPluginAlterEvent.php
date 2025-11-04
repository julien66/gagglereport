<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class DisplayVariantPluginAlterEvent.
 */
#[HookEvent(id: 'display_variant_plugin_alter', alter: 'display_variant_plugin')]
class DisplayVariantPluginAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::DISPLAY_VARIANT_PLUGIN_ALTER;
  }

}
