<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class UsernamePreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\UsernameEventVariables getVariables()
 */
final class UsernamePreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'username';
  }

}
