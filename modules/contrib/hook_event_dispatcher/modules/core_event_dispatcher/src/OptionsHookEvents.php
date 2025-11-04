<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Define events for options hooks.
 */
final class OptionsHookEvents {

  /**
   * Alters the list of options to be displayed for a field.
   *
   * This hook can notably be used to change the label of the empty option.
   *
   * @Event
   *
   * @var string
   *
   * @see \Drupal\core_event_dispatcher\Event\Options\OptionsListAlterEvent
   * @see hook_options_list_alter()
   */
  public const OPTIONS_LIST_ALTER = HookEventDispatcherInterface::PREFIX . 'options_list.alter';

}
