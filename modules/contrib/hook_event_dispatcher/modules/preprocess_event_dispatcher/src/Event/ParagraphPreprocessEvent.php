<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class ParagraphPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\ParagraphEventVariables getVariables()
 */
final class ParagraphPreprocessEvent extends AbstractPreprocessEntityEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'paragraph';
  }

}
