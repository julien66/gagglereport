<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityTranslationDeleteEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityTranslationInsertEvent;
use Drupal\entity_test\Entity\EntityTestMul;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityMulCrudEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityTranslationDeleteEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityTranslationInsertEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityMulCrudEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'entity_test',
    'language',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test callback.
   *
   * @throws \Exception
   */
  public function testEntityEvent(): void {
    $this->installEntitySchema('entity_test_mul');

    $storage = $this->container->get('entity_type.manager')->getStorage('entity_test_mul');

    $langcode = $this->randomMachineName();
    $config = $this->config('language.entity.' . $langcode);
    $config->setData([
      'id' => $langcode,
      'langcode' => $langcode,
      'status' => TRUE,
      'label' => $this->randomString(),
    ]);
    $config->save();

    $entity = $storage->create();
    $this->assertInstanceOf(EntityTestMul::class, $entity);
    $entity->save();

    // Test EntityTranslationInsertEvent.
    $this->listen(EntityHookEvents::ENTITY_TRANSLATION_INSERT, 'onEntityEvent');
    $translation = $entity->addTranslation($langcode);
    $translation->save();

    // Test EntityTranslationDeleteEvent.
    $this->listen(EntityHookEvents::ENTITY_TRANSLATION_DELETE, 'onEntityEvent');
    $entity->removeTranslation($langcode);
    $entity->save();
  }

  /**
   * Test EntityTranslation Event.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityTranslationDeleteEvent|\Drupal\core_event_dispatcher\Event\Entity\EntityTranslationInsertEvent $event
   *   The event.
   */
  public function onEntityEvent(EntityTranslationDeleteEvent|EntityTranslationInsertEvent $event): void {
    $this->assertInstanceOf(EntityTestMul::class, $event->getEntity());
  }

}
