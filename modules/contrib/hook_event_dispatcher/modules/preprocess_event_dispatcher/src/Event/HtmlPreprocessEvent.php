<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class HtmlPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\HtmlEventVariables getVariables()
 */
final class HtmlPreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'html';
  }

}
