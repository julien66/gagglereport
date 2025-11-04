<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityCreateEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityLoadEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityPredeleteEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityUpdateEvent;
use Drupal\entity_test\Entity\EntityTest;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityCrudEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\AbstractEntityEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityCreateEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityLoadEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityPredeleteEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityUpdateEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityCrudEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'entity_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test entity CRUD event.
   *
   * @throws \Exception
   */
  public function testEntityEvent(): void {
    $storage = $this->container->get('entity_type.manager')->getStorage('entity_test');

    // Test EntityCreateEvent.
    $this->listen(EntityHookEvents::ENTITY_CREATE, 'onEntityCreate');
    $entity = $storage->create();
    $this->assertInstanceOf(EntityTest::class, $entity);
    $this->assertEquals('Created', $entity->label());

    $this->installEntitySchema('entity_test');

    // Test EntityPresaveEvent.
    $this->listen(EntityHookEvents::ENTITY_PRE_SAVE, 'onEntityPresave', $this->atLeastOnce());

    // Test EntityInsertEvent.
    $this->listen(EntityHookEvents::ENTITY_INSERT, 'onEntityInsert');
    $entity->save();

    // Test EntityLoadEvent.
    $this->listen(EntityHookEvents::ENTITY_LOAD, 'onEntityLoad', $this->atLeastOnce());
    $entity = $storage->load($entity->id());
    $this->assertInstanceOf(EntityTest::class, $entity);

    // Test EntityUpdateEvent.
    $this->listen(EntityHookEvents::ENTITY_UPDATE, 'onEntityUpdate');
    $entity->setName('Updated');
    $entity->save();

    // Test EntityPredeleteEvent and EntityDeleteEvent.
    $this->listen([
      EntityHookEvents::ENTITY_PRE_DELETE,
      EntityHookEvents::ENTITY_DELETE,
    ], 'onEntityDelete', $this->exactly(2));

    $entity->delete();
  }

  /**
   * Test EntityCreateEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityCreateEvent $event
   *   The event.
   */
  public function onEntityCreate(EntityCreateEvent $event): void {
    $entity = $event->getEntity();
    $this->assertInstanceOf(EntityTest::class, $entity);
    $entity->setName('Created');
  }

  /**
   * Test EntityInsertEvent & EntityTranslationInsertEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent $event
   *   The event.
   */
  public function onEntityInsert(EntityInsertEvent $event): void {
    $entity = $event->getEntity();
    $this->assertEquals('Created', $entity->label());
  }

  /**
   * Test EntityLoadEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityLoadEvent $event
   *   The event.
   */
  public function onEntityLoad(EntityLoadEvent $event): void {
    foreach ($event->getEntities() as $entity) {
      $this->assertInstanceOf(EntityTest::class, $entity);
      $this->assertEquals('Created', $entity->label());
    }

    $this->assertEquals('entity_test', $event->getEntityTypeId());
  }

  /**
   * Test EntityPresaveEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent $event
   *   The event.
   */
  public function onEntityPresave(EntityPresaveEvent $event): void {
    $entity = $event->getEntity();
    if ($entity->isNew()) {
      $this->assertNull($event->getOriginalEntity());
      $this->assertEquals('Created', $event->getEntity()->label());
      return;
    }

    $this->assertNotNull($event->getOriginalEntity());
    $this->assertEquals('Created', $event->getOriginalEntity()->label());
    $this->assertEquals('Updated', $event->getEntity()->label());
  }

  /**
   * Test EntityUpdateEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityUpdateEvent $event
   *   The event.
   */
  public function onEntityUpdate(EntityUpdateEvent $event): void {
    $this->assertEquals('Created', $event->getOriginalEntity()->label());
    $this->assertEquals('Updated', $event->getEntity()->label());
  }

  /**
   * Test EntityDeleteEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent|\Drupal\core_event_dispatcher\Event\Entity\EntityPredeleteEvent $event
   *   The event.
   */
  public function onEntityDelete(EntityDeleteEvent|EntityPredeleteEvent $event): void {
    $this->assertEquals('Updated', $event->getEntity()->label());
  }

}
