<?php

namespace Drupal\core_event_dispatcher\Event\Theme;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class ThemeSuggestionsAlterIdEvent.
 */
#[HookEvent(id: 'theme_suggestions_alter:id', alter: 'theme_suggestions')]
class ThemeSuggestionsAlterIdEvent extends AbstractThemeSuggestionsEvent {

  /**
   * Returns the hook dispatcher type.
   *
   * @return string
   *   Dispatcher type.
   */
  public function getDispatcherType(): string {
    return 'hook_event_dispatcher.theme.suggestions_' . $this->getHook() . '_alter';
  }

}
