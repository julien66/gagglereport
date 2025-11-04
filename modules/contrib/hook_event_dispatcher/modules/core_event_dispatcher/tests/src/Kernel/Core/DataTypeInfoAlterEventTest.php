<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\DataTypeInfoAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class DataTypeInfoAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\DataTypeInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class DataTypeInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the DataTypeInfoAlterEvent.
   *
   * @throws \Exception
   */
  public function testDataTypeInfoAlterEvent(): void {
    $this->listen(CoreHookEvents::DATA_TYPE_INFO_ALTER, 'onDataTypeInfoAlter');

    $dataType = $this->container->get('typed_data_manager')->getDefinition('any');
    $this->assertEquals('Test altered', $dataType['label']);
  }

  /**
   * Callback for DataTypeInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\DataTypeInfoAlterEvent $event
   *   The event.
   */
  public function onDataTypeInfoAlter(DataTypeInfoAlterEvent $event): void {
    $dataTypes = &$event->getDataTypes();
    $this->assertNotEquals('Test altered', $dataTypes['any']['label']);

    $dataTypes['any']['label'] = 'Test altered';
  }

}
