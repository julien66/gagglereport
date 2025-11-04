<?php

namespace Drupal\Tests\field_event_dispatcher\Unit\Field;

/**
 * Class FormEntityViewDisplayEditAlterEventSubscriberTest.
 *
 * @covers \Drupal\field_event_dispatcher\EventSubscriber\Form\FormEntityDisplayEditAlterEventSubscriber
 *
 * @group hook_event_dispatcher
 * @group field_event_dispatcher
 */
class FormEntityViewDisplayEditAlterEventSubscriberTest extends AbstractFormEntityDisplayEditAlterEventSubscriberTestCase {

  /**
   * {@inheritdoc}
   */
  protected $formId = 'entity_view_display_edit_form';

}
