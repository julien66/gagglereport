<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\MailBackendInfoAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class MailBackendInfoAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\MailBackendInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class MailBackendInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test MailBackendInfoAlterEvent.
   *
   * @throws \Exception
   */
  public function testMailBackendInfoAlter(): void {
    $this->listen(CoreHookEvents::MAIL_BACKEND_INFO_ALTER, 'onMailBackendInfoAlter');

    $info = $this->container->get('plugin.manager.mail')->getDefinitions();
    $this->assertArrayNotHasKey('test_mail_collector', $info);
  }

  /**
   * Callback for MailBackendInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\MailBackendInfoAlterEvent $event
   *   The event.
   */
  public function onMailBackendInfoAlter(MailBackendInfoAlterEvent $event): void {
    $info = &$event->getInfo();
    $this->assertArrayHasKey('test_mail_collector', $info);
    unset($info['test_mail_collector']);
  }

}
