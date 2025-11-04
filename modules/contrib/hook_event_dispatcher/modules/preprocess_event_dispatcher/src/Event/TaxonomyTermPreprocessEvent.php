<?php

namespace Drupal\preprocess_event_dispatcher\Event;

/**
 * Class TaxonomyTermPreprocessEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\TaxonomyTermEventVariables getVariables()
 */
final class TaxonomyTermPreprocessEvent extends AbstractPreprocessEntityEvent {

  /**
   * {@inheritdoc}
   */
  public static function getHook(): string {
    return 'taxonomy_term';
  }

}
