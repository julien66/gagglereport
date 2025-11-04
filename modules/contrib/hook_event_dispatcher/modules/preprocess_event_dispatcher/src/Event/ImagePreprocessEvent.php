<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class ImagePreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\ImageEventVariables getVariables()
 */
final class ImagePreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'image';
  }

}
