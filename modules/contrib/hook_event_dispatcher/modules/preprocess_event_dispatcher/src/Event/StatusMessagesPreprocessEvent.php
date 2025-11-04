<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class StatusMessagesPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\StatusMessagesEventVariables getVariables()
 */
final class StatusMessagesPreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'status_messages';
  }

}
