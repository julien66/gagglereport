<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityBundleEventBase;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityBundleEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBundleEventBase
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBundleCreateEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBundleDeleteEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityBundleEventTest extends KernelTestBase {

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
   * Test EntityBundleEvent.
   *
   * @throws \Exception
   */
  public function testEntityBundleEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_BUNDLE_CREATE, 'onEntityBundle');
    $this->listen(EntityHookEvents::ENTITY_BUNDLE_CREATE, 'onEntityBundle');

    $entityBundleListener = $this->container->get('entity_bundle.listener');
    $entityBundleListener->onBundleCreate('bundle', 'entity_test');
    $entityBundleListener->onBundleDelete('bundle', 'entity_test');
  }

  /**
   * Callback for EntityBundleEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityBundleEventBase $event
   *   The event.
   */
  public function onEntityBundle(EntityBundleEventBase $event): void {
    $this->assertEquals('entity_test', $event->getEntityTypeId());
    $this->assertEquals('bundle', $event->getBundle());
  }

}
