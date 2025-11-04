<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Block;

use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\core_event_dispatcher\Event\Block\BlockBuildAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class BlockBuildAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Block\BlockViewBuilderAlterEventBase
 * @covers \Drupal\core_event_dispatcher\Event\Block\BlockBuildAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class BlockBuildAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'block',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the BlockBuildAlterEvent.
   *
   * @throws \Exception
   */
  public function testBlockBuildAlterEvent(): void {
    $this->listen(BlockHookEvents::BLOCK_BUILD_ALTER, 'onBlockBuildAlter');

    $entityTypeManager = $this->container->get('entity_type.manager');

    $block = $entityTypeManager->getStorage('block')->create([
      'plugin' => 'broken',
    ]);

    $viewBuilder = $entityTypeManager->getViewBuilder('block');
    $view = $viewBuilder->view($block);

    $this->assertArrayHasKey('other', $view);
    $this->assertEquals('some_build', $view['other']);
  }

  /**
   * Callback for BlockBuildAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Block\BlockBuildAlterEvent $event
   *   The event.
   */
  public function onBlockBuildAlter(BlockBuildAlterEvent $event): void {
    $build = &$event->getBuild();
    $build['other'] = 'some_build';

    $block = $event->getBlock();
    $this->assertEquals('broken', $block->getPluginId());
  }

}
