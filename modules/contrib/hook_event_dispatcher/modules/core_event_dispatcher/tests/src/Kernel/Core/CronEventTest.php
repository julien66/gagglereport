<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class CronEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\CronEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class CronEventTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the cron event.
   *
   * @throws \Exception
   */
  public function testCronEvent(): void {
    $called = FALSE;
    $this->container->get('event_dispatcher')
      ->addListener(CoreHookEvents::CRON, static function () use (&$called) {
        $called = TRUE;
      });

    $this->container->get('cron')->run();
    $this->assertTrue($called);
  }

}
