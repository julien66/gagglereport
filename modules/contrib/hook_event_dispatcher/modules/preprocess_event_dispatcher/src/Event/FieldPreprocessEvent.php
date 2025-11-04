<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class FieldPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\FieldEventVariables getVariables()
 */
final class FieldPreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'field';
  }

}
