<?php

namespace Drupal\Tests\path_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\path_event_dispatcher\Event\Path\PathDeleteEvent;
use Drupal\path_event_dispatcher\Event\Path\PathInsertEvent;
use Drupal\path_event_dispatcher\Event\Path\PathUpdateEvent;
use Drupal\path_event_dispatcher\PathHookEvents;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\path_event_dispatcher\Event\Path\AbstractPathEvent
 * @covers \Drupal\path_event_dispatcher\Event\Path\PathDeleteEvent
 * @covers \Drupal\path_event_dispatcher\Event\Path\PathInsertEvent
 * @covers \Drupal\path_event_dispatcher\Event\Path\PathUpdateEvent
 *
 * @group hook_event_dispatcher
 * @group path_event_dispatcher
 */
class PathEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'path_alias',
    'hook_event_dispatcher',
    'path_event_dispatcher',
  ];

  /**
   * The path alias entity.
   *
   * @var \Drupal\path_alias\PathAliasInterface
   */
  protected $pathAlias;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('path_alias');

    $this->pathAlias = $this->container->get('entity_type.manager')
      ->getStorage('path_alias')
      ->create([
        'id' => random_int(0, mt_getrandmax()),
        'path' => 'testPath',
        'alias' => 'testAlias',
      ]);
  }

  /**
   * Test PathInsertEvent.
   *
   * @throws \Exception
   */
  public function testPathInsertEvent(): void {
    $this->listen(PathHookEvents::PATH_INSERT, 'onPathInsert');
    $this->listen(PathHookEvents::PATH_UPDATE, 'onPathUpdate');
    $this->listen(PathHookEvents::PATH_DELETE, 'onPathDelete');

    $this->pathAlias->save();

    $this->pathAlias->setPath('updatedPath');
    $this->pathAlias->setAlias('updatedAlias');
    $this->pathAlias->save();

    $this->pathAlias->delete();
  }

  /**
   * Callback for PathInsertEvent.
   *
   * @param \Drupal\path_event_dispatcher\Event\Path\PathInsertEvent $event
   *   The event.
   */
  public function onPathInsert(PathInsertEvent $event): void {
    $this->assertEquals($this->pathAlias->id(), $event->getPid());
    $this->assertEquals($this->pathAlias->getPath(), $event->getSource());
    $this->assertEquals($this->pathAlias->getAlias(), $event->getAlias());
    $this->assertEquals($this->pathAlias->language()->getId(), $event->getLangcode());
  }

  /**
   * Callback for PathUpdateEvent.
   *
   * @param \Drupal\path_event_dispatcher\Event\Path\PathUpdateEvent $event
   *   The event.
   */
  public function onPathUpdate(PathUpdateEvent $event): void {
    $this->assertEquals('updatedPath', $event->getSource());
    $this->assertEquals('updatedAlias', $event->getAlias());
  }

  /**
   * Callback for PathDeleteEvent.
   *
   * @param \Drupal\path_event_dispatcher\Event\Path\PathDeleteEvent $event
   *   The event.
   */
  public function onPathDelete(PathDeleteEvent $event): void {
    $this->assertFalse($event->isRedirect());
  }

}
