<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Options;

use Drupal\Core\Field\Plugin\Field\FieldWidget\OptionsButtonsWidget;
use Drupal\core_event_dispatcher\Event\Options\OptionsListAlterEvent;
use Drupal\core_event_dispatcher\OptionsHookEvents;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\Tests\options\Kernel\OptionsFieldUnitTestBase;

/**
 * Class OptionsListAlterEvent.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Options\OptionsListAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 *
 * @see core_event_dispatcher_options_list_alter()
 */
class OptionsListAlterEventTest extends OptionsFieldUnitTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * @var \Drupal\entity_test\Entity\EntityTest
   */
  private EntityTest $entity;

  /**
   * Test OptionsListAlterEvent.
   *
   * @throws \Exception
   */
  public function testOptionsListAlterEvent(): void {
    $this->listen(OptionsHookEvents::OPTIONS_LIST_ALTER, 'onOptionsListAlter');

    $this->entity = EntityTest::create();
    $form = $this->container->get('entity.form_builder')->getForm($this->entity);
    $this->assertEquals('- Select something -', $form[$this->fieldName]['widget']['_none']['#title']);
  }

  /**
   * Callback for OptionsListAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Options\OptionsListAlterEvent $event
   *   The event.
   */
  public function onOptionsListAlter(OptionsListAlterEvent $event): void {
    $this->assertEquals($this->fieldName, $event->getFieldDefinition()->getName());
    $this->assertSame($this->entity, $event->getEntity());
    $this->assertInstanceOf(OptionsButtonsWidget::class, $event->getWidget());

    $options = &$event->getOptions();
    $this->assertArrayHasKey('_none', $options);
    $this->assertArrayHasKey(1, $options, 'Option 1 exists');
    $this->assertArrayHasKey(2, $options, 'Option 2 exists');
    $this->assertArrayHasKey(3, $options, 'Option 3 exists');
    $this->assertEquals('N/A', $options['_none']);
    $options['_none'] = '- Select something -';
  }

}
