<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityBaseFieldInfoEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityBaseFieldInfoEventTest extends KernelTestBase {

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
   * Field definitions.
   *
   * @var string[]
   */
  protected $fields = [
    'field_test1' => 'test',
    'field_test2' => 'otherTest',
  ];

  /**
   * Test the EntityBaseFieldInfoEvent.
   *
   * @throws \Exception
   */
  public function testEntityBaseFieldInfoEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_BASE_FIELD_INFO, 'onEntityBaseFieldInfo');
    $definitions = $this->container->get('entity_field.manager')
      ->getBaseFieldDefinitions('entity_test');

    foreach ($this->fields as $key => $value) {
      $this->assertArrayHasKey($key, $definitions);
      $this->assertEquals($value, $definitions[$key]);
    }
  }

  /**
   * Callback for EntityBaseFieldInfoEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoEvent $event
   *   The event.
   */
  public function onEntityBaseFieldInfo(EntityBaseFieldInfoEvent $event): void {
    $this->assertEquals('entity_test', $event->getEntityType()->id());
    $event->setFields($this->fields);
  }

}
