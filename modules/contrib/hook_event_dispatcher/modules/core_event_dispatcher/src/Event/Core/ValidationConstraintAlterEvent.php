<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class ValidationConstraintAlterEvent.
 */
#[HookEvent(id: 'validation_constraint_alter', alter: 'validation_constraint')]
class ValidationConstraintAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::VALIDATION_CONSTRAINT_ALTER;
  }

}
