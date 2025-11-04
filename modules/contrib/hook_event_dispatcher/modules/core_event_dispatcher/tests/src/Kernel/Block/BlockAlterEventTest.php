<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Block;

use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\core_event_dispatcher\Event\Block\BlockAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class BlockAccessEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Block\BlockAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class BlockAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test BlockAlterEvent.
   *
   * @throws \Exception
   */
  public function testBlockAlterEvent(): void {
    $this->listen(BlockHookEvents::BLOCK_ALTER, 'onBlockAlter');

    $definitions = $this->container->get('plugin.manager.block')->getDefinitions();
    $this->assertEquals('Test', $definitions['broken']['admin_label']);
    $this->assertArrayHasKey('test_plugin', $definitions);
    $this->assertEquals(['id' => 'test_plugin'], $definitions['test_plugin']);
  }

  /**
   * Callback for BlockAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Block\BlockAlterEvent $event
   *   The event.
   */
  public function onBlockAlter(BlockAlterEvent $event): void {
    $this->assertNull($event->getDefinition('not exist', FALSE));

    $definitions = &$event->getDefinitions();
    $definitions['broken']['admin_label'] = 'Test';

    $event->setDefinition('test_plugin', [
      'id' => 'test_plugin',
    ]);
  }

}
