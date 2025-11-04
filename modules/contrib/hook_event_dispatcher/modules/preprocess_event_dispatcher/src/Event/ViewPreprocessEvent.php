<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class ViewPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\ViewEventVariables getVariables()
 */
final class ViewPreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'views_view';
  }

}
