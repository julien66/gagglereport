<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for language hooks.
 */
final class LanguageHookEvents {

  /**
   * Alter the links generated to switch languages.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Language\LanguageSwitchLinksAlterEvent
   * @see core_event_dispatcher_language_switch_links_alter()
   * @see hook_language_switch_links_alter()
   *
   * @var string
   */
  public const LANGUAGE_SWITCH_LINKS_ALTER = HookEventDispatcherInterface::PREFIX . 'language.switch_links_alter';

}
