<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class NodePreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\NodeEventVariables getVariables()
 */
final class NodePreprocessEvent extends AbstractPreprocessEntityEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'node';
  }

}
