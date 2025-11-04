<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class QueueInfoAlterEvent.
 */
#[HookEvent(id: 'queue_info_alter', alter: 'queue_info')]
class QueueInfoAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * Get queues info.
   *
   * @return array
   *   Queue info.
   */
  public function &getQueues(): array {
    return $this->definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::QUEUE_INFO_ALTER;
  }

}
