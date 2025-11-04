<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class RebuildEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\RebuildEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class RebuildEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the rebuild event.
   *
   * @throws \Exception
   */
  public function testRebuildEvent(): void {
    $this->listen(CoreHookEvents::REBUILD, 'onRebuild');

    $this->container->get('module_handler')->invokeAll('rebuild');
  }

  /**
   * Callback for RebuildEvent.
   */
  public function onRebuild(): void {
  }

}
