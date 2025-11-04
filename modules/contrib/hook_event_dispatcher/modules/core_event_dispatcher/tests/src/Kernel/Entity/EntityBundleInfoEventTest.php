<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoAlterEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityBundleInfoEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoAlterEvent
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityBundleInfoEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test EntityBundleInfoEvent.
   *
   * @throws \Exception
   */
  public function testEntityBundleInfoEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_BUNDLE_INFO, 'onEntityBundleInfo');
    $this->listen(EntityHookEvents::ENTITY_BUNDLE_INFO_ALTER, 'onEntityBundleInfoAlter');
    $info = $this->container->get('entity_type.bundle.info')
      ->getBundleInfo('test');
    $this->assertEquals([
      'test' => [
        'label' => 'Test',
        'translatable' => FALSE,
      ],
    ], $info);
  }

  /**
   * Callback for EntityBundleInfoEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoEvent $event
   *   The event.
   */
  public function onEntityBundleInfo(EntityBundleInfoEvent $event): void {
    $event->addBundleInfo('test', 'test', [
      'label' => 'Original',
      'translatable' => TRUE,
    ]);
  }

  /**
   * Callback for EntityBundleInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoAlterEvent $event
   *   The event.
   */
  public function onEntityBundleInfoAlter(EntityBundleInfoAlterEvent $event): void {
    $bundles = &$event->getBundles();
    $this->assertEquals('Original', $bundles['test']['test']['label']);

    $bundles['test']['test']['label'] = 'Test';
    $event->alterBundleInfo('test', 'test', 'translatable', FALSE);
  }

}
