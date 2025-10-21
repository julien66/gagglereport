<?php

namespace Drupal\poll;

use Drupal\content_translation\ContentTranslationHandler;

/**
 * Defines the translation handler for poll.
 */
class PollChoiceTranslationHandler extends ContentTranslationHandler {

  /**
   * {@inheritdoc}
   */
  public function getFieldDefinitions() {
    return [];
  }

}
