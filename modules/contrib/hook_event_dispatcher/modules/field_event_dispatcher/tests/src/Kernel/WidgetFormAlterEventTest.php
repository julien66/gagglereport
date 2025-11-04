<?php

namespace Drupal\Tests\field_event_dispatcher\Kernel;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormState;
use Drupal\field_event_dispatcher\Event\Field\WidgetCompleteFormAlterEvent;
use Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementFormAlterEvent;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class WidgetFormAlterEventTest.
 *
 * @covers \Drupal\field_event_dispatcher\Event\Field\WidgetCompleteFormAlterEvent
 * @covers \Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementFormAlterEvent
 * @covers \Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementTypeFormAlterEvent
 *
 * @group hook_event_dispatcher
 * @group field_event_dispatcher
 */
class WidgetFormAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  protected const ENTITY_TYPE_ID = 'entity_test_base_field_display';

  protected const TEST_DISPLAY_CONFIGURABLE = 'test_display_configurable';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'field',
    'text',
    'user',
    'entity_test',
    'hook_event_dispatcher',
    'field_event_dispatcher',
  ];

  /**
   * The entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * The entity form display.
   *
   * @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface
   */
  protected $entityFormDisplay;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $entityTypeManager = $this->container->get('entity_type.manager');
    $this->entity = $entityTypeManager->getStorage(self::ENTITY_TYPE_ID)->create();

    $this->entityFormDisplay = $entityTypeManager->getStorage('entity_form_display')->create([
      'targetEntityType' => self::ENTITY_TYPE_ID,
      'bundle' => self::ENTITY_TYPE_ID,
    ]);
    $this->entityFormDisplay->setComponent(self::TEST_DISPLAY_CONFIGURABLE, []);
  }

  /**
   * Test WidgetSingleElementFormAlterEvent.
   *
   * @group legacy
   *
   * @throws \Exception
   */
  public function testWidgetSingleElementFormAlterEvent(): void {
    $this->listen([
      FieldHookEvents::WIDGET_SINGLE_ELEMENT_FORM_ALTER,
      'hook_event_dispatcher.widget_single_element_text_textfield.alter',
    ], 'onWidgetSingleElementFormAlter', $this->exactly(2));
    $this->listen(FieldHookEvents::WIDGET_COMPLETE_FORM_ALTER, 'onWidgetCompleteFormAlter');

    $form = [];
    $formState = new FormState();
    $formState->set('test', TRUE);

    $this->assertInstanceOf(FieldableEntityInterface::class, $this->entity);
    $this->entityFormDisplay->buildForm($this->entity, $form, $formState);

    $this->assertArrayHasKey(self::TEST_DISPLAY_CONFIGURABLE, $form);

    $this->assertArrayHasKey('test', $form[self::TEST_DISPLAY_CONFIGURABLE]);
    $this->assertTrue($form[self::TEST_DISPLAY_CONFIGURABLE]['test']);

    $widget = $form[self::TEST_DISPLAY_CONFIGURABLE]['widget'][0];
    $this->assertArrayHasKey('other', $widget);
    $this->assertEquals('key', $widget['other']);
    $this->assertArrayHasKey('other_element', $widget);
    $this->assertEquals('key', $widget['other_element']);
  }

  /**
   * Callback for WidgetSingleElementFormAlterEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementFormAlterEvent $event
   *   The event.
   */
  public function onWidgetSingleElementFormAlter(WidgetSingleElementFormAlterEvent $event): void {
    $formState = $event->getFormState();
    $this->assertTrue($formState->has('test'));
    $this->assertTrue($formState->get('test'));

    $context = $event->getContext();
    $widget = $context['widget'];
    $this->assertInstanceOf(WidgetInterface::class, $widget);
    $this->assertEquals('text_textfield', $widget->getPluginId());

    $element = &$event->getElement();
    $element['other'] = 'key';
  }

  /**
   * Callback for WidgetCompleteFormAlterEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\WidgetCompleteFormAlterEvent $event
   *   The event.
   */
  public function onWidgetCompleteFormAlter(WidgetCompleteFormAlterEvent $event): void {
    $formState = $event->getFormState();
    $this->assertTrue($formState->has('test'));
    $this->assertTrue($formState->get('test'));

    $context = $event->getContext();
    $widget = $context['widget'];
    $this->assertInstanceOf(WidgetInterface::class, $widget);
    $this->assertEquals('text_textfield', $widget->getPluginId());

    $widgetCompleteForm = &$event->getWidgetCompleteForm();
    $widgetCompleteForm['test'] = TRUE;

    $elements = &$event->getElements();
    $elements[0]['other_element'] = 'key';
  }

}
