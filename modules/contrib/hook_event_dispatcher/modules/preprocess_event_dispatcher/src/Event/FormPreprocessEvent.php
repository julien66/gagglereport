<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class FormPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\FormEventVariables getVariables()
 */
final class FormPreprocessEvent extends AbstractPreprocessEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'form';
  }

}
