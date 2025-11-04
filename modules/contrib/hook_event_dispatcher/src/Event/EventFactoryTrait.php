<?php

namespace Drupal\hook_event_dispatcher\Event;

/**
 * Trait EventFactoryTrait.
 */
trait EventFactoryTrait {

  /**
   * {@inheritdoc}
   */
  public static function create(&...$args): EventFactoryInterface {
    return new static(...$args);
  }

}
