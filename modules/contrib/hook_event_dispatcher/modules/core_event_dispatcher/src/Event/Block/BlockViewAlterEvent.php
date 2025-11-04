<?php

namespace Drupal\core_event_dispatcher\Event\Block;

use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class BlockViewAlterEvent.
 */
#[HookEvent(id: 'block_view_alter', alter: 'block_view')]
class BlockViewAlterEvent extends BlockViewBuilderAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return BlockHookEvents::BLOCK_VIEW_ALTER;
  }

}
