<?php

namespace Drupal\Tests\field_event_dispatcher\Kernel;

use Drupal\field_event_dispatcher\Event\Field\FieldFormatterInfoAlterEvent;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class FieldFormatterInfoAlterEventTest.
 *
 * @covers \Drupal\field_event_dispatcher\Event\Field\FieldFormatterInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group field_event_dispatcher
 */
class FieldFormatterInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'field_event_dispatcher',
  ];

  /**
   * FieldFormatterInfoAlterEvent array alter test.
   *
   * This tests altering the field formatter type definitions array.
   *
   * @throws \Exception
   */
  public function testFieldFormatterInfoAlter(): void {
    $this->listen(FieldHookEvents::FIELD_FORMATTER_INFO_ALTER, 'onFieldFormatterInfoAlter');

    $definition = $this->container->get('plugin.manager.field.formatter')->getDefinition('string');
    $this->assertEquals('test', $definition['label']);
  }

  /**
   * Callback for FieldFormatterInfoAlterEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\FieldFormatterInfoAlterEvent $event
   *   The event.
   */
  public function onFieldFormatterInfoAlter(FieldFormatterInfoAlterEvent $event): void {
    $info = &$event->getInfo();
    $info['string']['label'] = 'test';
  }

}
