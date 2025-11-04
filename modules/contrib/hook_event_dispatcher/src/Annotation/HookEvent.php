<?php

namespace Drupal\hook_event_dispatcher\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines hook_event annotation object.
 *
 * @Annotation
 */
class HookEvent extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The hook to which this event is attached.
   *
   * The value must be without `hook_` prefix.
   *
   * @var string
   */
  public $hook;

  /**
   * The alter hook to which this event is attached.
   *
   * The value must be without `hook_` prefix and `_alter` suffix.
   *
   * @var string
   */
  public $alter;

}
