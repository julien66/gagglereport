<?php

namespace Drupal\form_api_example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Example ajax add remove buttons.
 *
 * This example demonstrates using ajax callbacks to add people's names
 * to a list of picnic attendees with an option to remove specific people.
 */
class AjaxAddRemoveElements extends FormBase {

  /**
   * Required by FormBase.
   */
  public function getFormId() {
    return 'form_api_example_ajax-add-remove-elements';
  }

  /**
   * Form with 'add more' and 'remove' buttons.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('This example shows an add-more button and a remove button for each specific element.'),
    ];

    // Get the number of names in the form already.
    $num_lines = $form_state->get('num_lines');
    // We have to ensure that there is at least one name field.
    if ($num_lines === NULL) {
      $form_state->set('num_lines', 1);
      $num_lines = $form_state->get('num_lines');
    }

    // Get a list of fields that were removed.
    $removed_fields = $form_state->get('removed_fields');
    // If no fields have been removed yet we use an empty array.
    if ($removed_fields === NULL) {
      $form_state->set('removed_fields', []);
      $removed_fields = $form_state->get('removed_fields');
    }

    $form['#tree'] = TRUE;
    $form['names_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('People coming to picnic'),
      '#prefix' => '<div id="names-fieldset-wrapper">',
      '#suffix' => '</div>',
    ];

    for ($i = 0; $i < $num_lines; $i++) {
      // Check if field was removed.
      if (in_array($i, $removed_fields)) {
        // Skip if field was removed and move to the next field.
        continue;
      }

      /* Create a new fieldset for each person
       * where we can add first and last name
       */
      // Fieldset title.
      $form['names_fieldset'][$i] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Person') . ' ' . ($i + 1),
      ];
      // Date.
      $form['names_fieldset'][$i]['first_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('First name'),
      ];
      // Amount.
      $form['names_fieldset'][$i]['last_name'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Last name'),
      ];
      $form['names_fieldset'][$i]['actions'] = [
        '#type' => 'submit',
        '#value' => $this->t('Remove'),
        '#name' => $i,
        '#submit' => ['::removeCallback'],
        '#ajax' => [
          'callback' => '::addMoreCallback',
          'wrapper' => 'names-fieldset-wrapper',
        ],
      ];
    }

    $form['names_fieldset']['actions'] = [
      '#type' => 'actions',
    ];

    $form['names_fieldset']['actions']['add_name'] = [
      '#type' => 'submit',
      '#value' => $this->t('Add one more'),
      '#submit' => ['::addOne'],
      '#ajax' => [
        'callback' => '::addMoreCallback',
        'wrapper' => 'names-fieldset-wrapper',
      ],
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;

  }

  /**
   * Callback for both ajax-enabled buttons.
   *
   * Selects and returns the fieldset with the names in it.
   */
  public function addMoreCallback(array &$form, FormStateInterface $form_state) {
    return $form['names_fieldset'];
  }

  /**
   * Submit handler for the "add-one-more" button.
   *
   * Increments the max counter and causes a rebuild.
   */
  public function addOne(array &$form, FormStateInterface $form_state) {
    $num_field = $form_state->get('num_lines');
    $add_button = $num_field + 1;
    $form_state->set('num_lines', $add_button);
    $form_state->setRebuild();
  }

  /**
   * Submit handler for the "remove" button.
   *
   * Removes the corresponding line.
   */
  public function removeCallback(array &$form, FormStateInterface $form_state) {
    /*
     * We use the name of the remove button to find
     * the element we want to remove
     * Line 72: '#name' => $i,.
     */
    $trigger = $form_state->getTriggeringElement();
    $indexToRemove = $trigger['#name'];

    // Remove the fieldset from $form (the easy way)
    unset($form['names_fieldset'][$indexToRemove]);

    // Remove the fieldset from $form_state (the hard way)
    // First fetch the fieldset, then edit it, then set it again
    // Form API does not allow us to directly edit the field.
    $namesFieldset = $form_state->getValue('names_fieldset');
    unset($namesFieldset[$indexToRemove]);
    // $form_state->unsetValue('names_fieldset');
    $form_state->setValue('names_fieldset', $namesFieldset);

    // Keep track of removed fields so we can add new fields at the bottom
    // Without this they would be added where a value was removed.
    $removed_fields = $form_state->get('removed_fields');
    $removed_fields[] = $indexToRemove;
    $form_state->set('removed_fields', $removed_fields);

    // Rebuild form_state.
    $form_state->setRebuild();
  }

  /**
   * Required by FormBase.
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * Required by FormBase.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

  }

}
