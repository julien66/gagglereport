<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class PagePreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\PageEventVariables getVariables()
 */
final class PagePreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'page';
  }

}
