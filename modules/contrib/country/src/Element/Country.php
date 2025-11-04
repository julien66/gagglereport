<?php

namespace Drupal\country\Element;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\Select;

/**
 * Provides a selected form element.
 *
 * @FormElement("country")
 */
class Country extends Select {

  /**
   * {@inheritdoc}
   */
  public static function processSelect(&$element, FormStateInterface $form_state, &$complete_form) {
    $element['#options'] = \Drupal::service('country.field.manager')->getList();
    return parent::processSelect($element, $form_state, $complete_form);
  }

}
