<?php

namespace Drupal\core_event_dispatcher\Event\Block;

use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class BlockAlterEvent.
 */
#[HookEvent(id: 'block_alter', alter: 'block')]
class BlockAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return BlockHookEvents::BLOCK_ALTER;
  }

}
