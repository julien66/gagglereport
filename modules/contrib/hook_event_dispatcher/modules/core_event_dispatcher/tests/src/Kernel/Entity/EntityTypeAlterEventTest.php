<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityTypeAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityTypeAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityTypeAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityTypeAlterEventTest extends KernelTestBase {

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
   * Test the EntityTypeAlterEvent.
   *
   * @throws \Exception
   */
  public function testEntityTypeAlterEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_TYPE_ALTER, 'onEntityTypeAlter');
    $definition = $this->container->get('entity_type.manager')
      ->getDefinition('entity_test');
    $this->assertEquals('my custom permission', $definition->get('admin_permission'));
  }

  /**
   * Callback for EntityTypeAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityTypeAlterEvent $event
   *   The event.
   */
  public function onEntityTypeAlter(EntityTypeAlterEvent $event): void {
    $entityTypes = &$event->getEntityTypes();
    $entityTypes['entity_test']->set('admin_permission', 'my custom permission');
  }

}
