<?php

namespace Drupal\core_event_dispatcher\Event\Block;

use Drupal\block\BlockInterface;
use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Access\AccessResultNeutral;
use Drupal\Core\Session\AccountInterface;
use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\AccessEventInterface;
use Drupal\hook_event_dispatcher\Event\AccessEventTrait;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class BlockAccessEvent.
 */
#[HookEvent(id: 'block_access', hook: 'block_access')]
class BlockAccessEvent extends Event implements EventInterface, AccessEventInterface, HookReturnInterface {

  use AccessEventTrait;

  /**
   * BlockBuildAlterEvent constructor.
   *
   * @param \Drupal\block\BlockInterface $block
   *   The block instance.
   * @param string $operation
   *   The operation to be performed.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account for which to check access.
   */
  public function __construct(protected readonly BlockInterface $block, string $operation, AccountInterface $account) {
    $this->operation = $operation;
    $this->account = $account;
    $this->accessResult = AccessResultNeutral::neutral();
  }

  /**
   * Get the block instance.
   *
   * @return \Drupal\block\BlockInterface
   *   The block instance.
   */
  public function getBlock(): BlockInterface {
    return $this->block;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return BlockHookEvents::BLOCK_ACCESS;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getAccessResult();
  }

}
