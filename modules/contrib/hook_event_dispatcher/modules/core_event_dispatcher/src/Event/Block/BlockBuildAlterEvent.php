<?php

namespace Drupal\core_event_dispatcher\Event\Block;

use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class BlockBuildAlterEvent.
 */
#[HookEvent(id: 'block_build_alter', alter: 'block_build')]
class BlockBuildAlterEvent extends BlockViewBuilderAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return BlockHookEvents::BLOCK_BUILD_ALTER;
  }

}
