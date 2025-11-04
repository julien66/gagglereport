<?php

namespace Drupal\core_event_dispatcher\Event\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class FormBaserAlterEvent.
 */
#[HookEvent(id: 'form_base_alter', alter: 'form')]
class FormBaseAlterEvent extends AbstractFormEvent {

  /**
   * The base form id.
   *
   * @var string
   */
  private string $baseFormId;

  /**
   * FormBaseAlterEvent constructor.
   *
   * @param array $form
   *   Nested array of form elements that comprise the form.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The current state of the form. The arguments that
   *   \Drupal::formBuilder()->getForm() was originally called with are
   *   available in the array $form_state->getBuildInfo()['args'].
   * @param string $formId
   *   String representing the name of the form itself. Typically this is the
   *   name of the function that generated the form.
   *
   * @SuppressWarnings(PHPMD.ElseExpression)
   */
  public function __construct(
    array &$form,
    FormStateInterface $formState,
    string $formId,
  ) {
    parent::__construct($form, $formState, $formId);
    $buildInfo = $formState->getBuildInfo();

    if (empty($buildInfo['base_form_id'])) {
      $this->stopPropagation();
    }
    else {
      $this->baseFormId = $buildInfo['base_form_id'];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return 'hook_event_dispatcher.form_base_' . $this->getBaseFormId() . '.alter';
  }

  /**
   * Get the base form id.
   *
   * @return string
   *   The base form id.
   */
  public function getBaseFormId(): string {
    return $this->baseFormId;
  }

}
