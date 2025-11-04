<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityBaseFieldInfoAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityBaseFieldInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the EntityBaseFieldInfoAlterEvent.
   *
   * @throws \Exception
   */
  public function testEntityBaseFieldInfoAlterEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_BASE_FIELD_INFO_ALTER, 'onEntityBaseFieldInfoAlter');

    $definitions = $this->container
      ->get('entity_field.manager')
      ->getBaseFieldDefinitions('entity_test');

    $this->assertArrayHasKey('field_test', $definitions);
    $this->assertEquals('test_altered', $definitions['field_test']);
  }

  /**
   * Callback for EntityBaseFieldInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoAlterEvent $event
   *   The event.
   */
  public function onEntityBaseFieldInfoAlter(EntityBaseFieldInfoAlterEvent $event): void {
    $fields = &$event->getFields();
    $fields['field_test'] = 'test_altered';

    $this->assertEquals('entity_test', $event->getEntityType()->id());
  }

}
