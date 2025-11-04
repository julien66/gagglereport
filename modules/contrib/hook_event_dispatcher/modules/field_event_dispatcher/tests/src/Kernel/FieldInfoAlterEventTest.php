<?php

namespace Drupal\Tests\field_event_dispatcher\Kernel;

use Drupal\field_event_dispatcher\Event\Field\FieldInfoAlterEvent;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class FieldInfoAlterEventTest.
 *
 * @group hook_event_dispatcher
 * @group field_event_dispatcher
 *
 * @see \field_event_dispatcher_field_info_alter()
 */
class FieldInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'field_event_dispatcher',
  ];

  /**
   * FieldInfoAlterEvent array alter test.
   *
   * This tests altering the field plugin type definitions array.
   *
   * @throws \Exception
   */
  public function testFieldInfoAlter(): void {
    $this->listen(FieldHookEvents::FIELD_INFO_ALTER, 'onFieldInfoAlter');

    $definition = $this->container->get('plugin.manager.field.field_type')->getDefinition('test_field');
    $this->assertEquals('test_widget', $definition['default_widget']);
  }

  /**
   * Callback for FieldInfoAlterEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\FieldInfoAlterEvent $event
   *   The event.
   */
  public function onFieldInfoAlter(FieldInfoAlterEvent $event): void {
    $info = &$event->getInfo();
    $info['test_field']['default_widget'] = 'test_widget';
  }

}
