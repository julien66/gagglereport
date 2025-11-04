<?php

namespace Drupal\hook_event_dispatcher\Event;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Trait AccessEventTrait.
 */
trait AccessEventTrait {

  /**
   * The operation to be performed.
   *
   * @var string
   */
  protected $operation;

  /**
   * The account for which to check access.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The access result.
   *
   * @var \Drupal\Core\Access\AccessResultInterface
   */
  protected AccessResultInterface $accessResult;

  /**
   * {@inheritdoc}
   */
  public function getOperation(): string {
    return $this->operation;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccount(): AccountInterface {
    return $this->account;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccessResult(): AccessResultInterface {
    return $this->accessResult ?? AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  public function setAccessResult(AccessResultInterface $accessResult): void {
    $this->accessResult = $accessResult;
  }

  /**
   * {@inheritdoc}
   */
  public function addAccessResult(AccessResultInterface $accessResult): void {
    $this->accessResult = $this->getAccessResult()->orIf($accessResult);
  }

}
