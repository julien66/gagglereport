<?php

namespace Drupal\Tests\webform_event_dispatcher\Kernel;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormState;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementAlterEvent;
use Drupal\webform_event_dispatcher\WebformHookEvents;

/**
 * Class WebformElementAlterEventTest.
 *
 * @covers \Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementAlterEvent
 * @covers \Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementTypeAlterEvent
 *
 * @requires module webform
 * @group hook_event_dispatcher
 * @group webform_event_dispatcher
 */
class WebformElementAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'path_alias',
    'webform',
    'webform_test_element',
    'hook_event_dispatcher',
    'webform_event_dispatcher',
  ];

  /**
   * Test WebformElementAlterEvent.
   *
   * @group legacy
   *
   * @throws \Exception
   */
  public function testWebformElementAlterEvent(): void {
    $this->listen([
      WebformHookEvents::WEBFORM_ELEMENT_ALTER,
      'hook_event_dispatcher.webform.element_webform_test_element.alter',
    ], 'onWebformElementAlter', $this->exactly(3));

    $this->installEntitySchema('user');
    $this->installSchema('webform', 'webform');
    $this->installConfig(['webform', 'webform_test_element']);

    $entityTypeManager = $this->container->get('entity_type.manager');

    $submission = $entityTypeManager->getStorage('webform_submission')->create([
      'webform_id' => 'test_element_plugin',
    ]);

    $formState = new FormState();
    $formState->set('form_display', $entityTypeManager->getStorage('entity_form_display')->create([
      'targetEntityType' => 'webform_submission',
      'bundle' => 'test_element_plugin',
    ]));
    $formState->set('test', TRUE);

    $submissionForm = $entityTypeManager->getFormObject('webform_submission', 'add');
    $submissionForm->setEntity($submission);

    $this->assertInstanceOf(EntityForm::class, $submissionForm);
    $form = $submissionForm->form([], $formState->setFormObject($submissionForm));
    $this->assertArrayHasKey('test', $form['elements']['test']);
    $this->assertTrue($form['elements']['test']['test']);
  }

  /**
   * Callback for WebformElementAlterEvent.
   *
   * @param \Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementAlterEvent $event
   *   The event.
   */
  public function onWebformElementAlter(WebformElementAlterEvent $event): void {
    $formState = $event->getFormState();
    $this->assertTrue($formState->has('test'));
    $this->assertTrue($formState->get('test'));

    $context = $event->getContext();
    $this->assertEquals('test_element_plugin', $context['form']['#webform_id']);

    $element = &$event->getElement();
    $element['test'] = TRUE;
  }

}
