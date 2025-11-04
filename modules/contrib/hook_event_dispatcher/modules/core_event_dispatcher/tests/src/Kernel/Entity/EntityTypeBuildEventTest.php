<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityTypeBuildEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityTypeBuildEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityTypeBuildEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityTypeBuildEventTest extends KernelTestBase {

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
   * Test the EntityTypeBuildEvent.
   *
   * @throws \Exception
   */
  public function testEntityTypeBuildEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_TYPE_BUILD, 'onEntityTypeBuild');

    $definition = $this->container->get('entity_type.manager')->getDefinition('entity_test');
    $this->assertEquals('my custom permission', $definition->getAdminPermission());
  }

  /**
   * Callback for EntityTypeBuildEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityTypeBuildEvent $event
   *   The event.
   */
  public function onEntityTypeBuild(EntityTypeBuildEvent $event): void {
    $entityTypes = &$event->getEntityTypes();
    $entityTypes['entity_test']->set('admin_permission', 'my custom permission');
  }

}
