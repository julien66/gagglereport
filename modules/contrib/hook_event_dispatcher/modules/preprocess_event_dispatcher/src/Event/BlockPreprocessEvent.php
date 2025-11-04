<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class BlockPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\BlockEventVariables getVariables()
 */
final class BlockPreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'block';
  }

}
