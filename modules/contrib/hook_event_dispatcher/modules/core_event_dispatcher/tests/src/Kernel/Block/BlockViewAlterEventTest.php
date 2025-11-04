<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Block;

use Drupal\block\BlockViewBuilder;
use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\core_event_dispatcher\Event\Block\BlockViewAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class BlockAccessEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Block\BlockViewBuilderAlterEventBase
 * @covers \Drupal\core_event_dispatcher\Event\Block\BlockViewAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class BlockViewAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'block',
    'block_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test BlockViewAlterEvent.
   *
   * @throws \Exception
   */
  public function testBlockViewAlterEvent(): void {
    $this->listen(BlockHookEvents::BLOCK_VIEW_ALTER, 'onBlockViewAlter');

    $this->installConfig('block_test');
    $view = BlockViewBuilder::lazyBuilder('test_block', 'full');
    $this->assertArrayHasKey('test', $view);
    $this->assertTrue($view['test']);
  }

  /**
   * Callback for BlockViewAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Block\BlockViewAlterEvent $event
   *   The event.
   */
  public function onBlockViewAlter(BlockViewAlterEvent $event): void {
    $this->assertEquals('test_html', $event->getBlock()->getPluginId());

    $build = &$event->getBuild();
    $build['test'] = TRUE;
  }

}
