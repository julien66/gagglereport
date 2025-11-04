<?php

namespace Drupal\core_event_dispatcher\Event\Theme;

use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class ThemeSuggestionsAlterEvent.
 */
#[HookEvent(id: 'theme_suggestions_alter', alter: 'theme_suggestions')]
class ThemeSuggestionsAlterEvent extends AbstractThemeSuggestionsEvent {

  /**
   * Returns the hook dispatcher type.
   *
   * @return string
   *   Dispatcher type.
   */
  public function getDispatcherType(): string {
    return ThemeHookEvents::THEME_SUGGESTIONS_ALTER;
  }

}
