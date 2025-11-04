<?php

namespace Drupal\Tests\field_event_dispatcher\Unit\Field;

/**
 * Class FormEntityFormDisplayEditAlterEventSubscriberTest.
 *
 * @covers \Drupal\field_event_dispatcher\EventSubscriber\Form\FormEntityDisplayEditAlterEventSubscriber
 *
 * @group hook_event_dispatcher
 * @group field_event_dispatcher
 */
class FormEntityFormDisplayEditAlterEventSubscriberTest extends AbstractFormEntityDisplayEditAlterEventSubscriberTestCase {

  /**
   * {@inheritdoc}
   */
  protected $formId = 'entity_form_display_edit_form';

}
