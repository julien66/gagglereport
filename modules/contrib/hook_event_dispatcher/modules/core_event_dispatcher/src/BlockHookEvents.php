<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for block hooks.
 */
final class BlockHookEvents {

  /**
   * Alter the result of \Drupal\Core\Block\BlockBase::build().
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Block\BlockViewAlterEvent
   * @see core_event_dispatcher_block_view_alter()
   * @see hook_block_view_alter()
   *
   * @var string
   */
  public const BLOCK_VIEW_ALTER = HookEventDispatcherInterface::PREFIX . 'block_view.alter';

  /**
   * Alter the result of \Drupal\Core\Block\BlockBase::build().
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Block\BlockBuildAlterEvent
   * @see core_event_dispatcher_block_build_alter()
   * @see hook_block_build_alter()
   *
   * @var string
   */
  public const BLOCK_BUILD_ALTER = HookEventDispatcherInterface::PREFIX . 'block_build.alter';

  /**
   * Control access to a block instance.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Block\BlockAccessEvent
   * @see core_event_dispatcher_block_access()
   * @see hook_block_access()
   *
   * @var string
   */
  public const BLOCK_ACCESS = HookEventDispatcherInterface::PREFIX . 'block.access';

  /**
   * Allow modules to alter the block plugin definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Block\BlockAlterEvent
   * @see core_event_dispatcher_block_alter()
   * @see hook_block_alter()
   *
   * @var string
   */
  public const BLOCK_ALTER = HookEventDispatcherInterface::PREFIX . 'block.alter';

}
