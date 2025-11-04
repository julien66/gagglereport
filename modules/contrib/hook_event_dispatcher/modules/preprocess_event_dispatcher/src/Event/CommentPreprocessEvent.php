<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class CommentPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\CommentEventVariables getVariables()
 */
final class CommentPreprocessEvent extends AbstractPreprocessEntityEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'comment';
  }

}
