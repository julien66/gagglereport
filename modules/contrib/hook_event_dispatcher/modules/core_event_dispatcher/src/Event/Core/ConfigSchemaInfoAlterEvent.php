<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class ConfigSchemaInfoAlterEvent.
 */
#[HookEvent(id: 'config_schema_info_alter', alter: 'config_schema_info')]
class ConfigSchemaInfoAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::CONFIG_SCHEMA_INFO_ALTER;
  }

}
