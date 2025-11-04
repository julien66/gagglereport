<?php

namespace Drupal\preprocess_event_dispatcher\Event;

use function array_filter;
use function implode;

/**
 * Class AbstractPreprocessEntityEvent.
 *
 * @method \Drupal\preprocess_event_dispatcher\Variables\AbstractEntityEventVariables getVariables()
 */
abstract class AbstractPreprocessEntityEvent extends AbstractPreprocessEvent implements PreprocessEntityEventInterface {

  /**
   * {@inheritdoc}
   */
  public static function name(string $bundle = '', string $viewMode = ''): string {
    return implode('.', array_filter([parent::name(), $bundle, $viewMode]));
  }

}
