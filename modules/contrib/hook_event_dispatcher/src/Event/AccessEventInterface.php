<?php

namespace Drupal\hook_event_dispatcher\Event;

use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Interface AccessEventInterface.
 */
interface AccessEventInterface {

  /**
   * Gets the operation to be performed.
   *
   * @return string
   *   The operation to be performed, or NULL if the event is not about an
   */
  public function getOperation(): string;

  /**
   * Gets the account for which to check access.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   The account for which to check access.
   */
  public function getAccount(): AccountInterface;

  /**
   * Gets the access result.
   *
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function getAccessResult(): AccessResultInterface;

  /**
   * Sets the access result.
   *
   * @param \Drupal\Core\Access\AccessResultInterface $accessResult
   *   The access result.
   */
  public function setAccessResult(AccessResultInterface $accessResult): void;

  /**
   * Adds the access result.
   *
   * @param \Drupal\Core\Access\AccessResultInterface $accessResult
   *   The access result.
   */
  public function addAccessResult(AccessResultInterface $accessResult): void;

}
