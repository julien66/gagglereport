<?php

namespace Drupal\preprocess_event_dispatcher\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\preprocess_event_dispatcher\Variables\AbstractEventVariables;

/**
 * Class AbstractPreprocessEvent.
 */
abstract class AbstractPreprocessEvent extends Event implements PreprocessEventInterface {

  /**
   * PreprocessEvent constructor.
   *
   * @param \Drupal\preprocess_event_dispatcher\Variables\AbstractEventVariables $variables
   *   The variables.
   */
  public function __construct(protected readonly AbstractEventVariables $variables) {
  }

  /**
   * {@inheritdoc}
   */
  public static function name(): string {
    return static::DISPATCH_NAME_PREFIX . static::getHook();
  }

  /**
   * {@inheritdoc}
   */
  public function getVariables(): AbstractEventVariables {
    return $this->variables;
  }

}
