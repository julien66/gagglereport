<?php

namespace Drupal\poll;

use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface defining an poll entity.
 */
interface PollChoiceInterface extends ContentEntityInterface {

  /**
   * Sets the choice for the poll choice.
   *
   * @param string $choice
   *   The poll choice.
   *
   * @return static
   *   The class instance that this method is called on.
   */
  public function setChoice($choice);

  /**
   * Whether or not the choice must be saved when the poll is saved.
   *
   * @param null|bool $new_value
   *   Pass FALSE or TRUE to change the current value.
   *
   * @return bool
   *   Returns the current value.
   */
  public function needsSaving($new_value = NULL);

}
