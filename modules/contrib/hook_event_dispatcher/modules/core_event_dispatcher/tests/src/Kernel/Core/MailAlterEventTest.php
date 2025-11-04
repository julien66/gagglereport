<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\MailAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class MailAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\MailAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class MailAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test MailAlterEvent.
   *
   * @throws \Exception
   */
  public function testMailAlter(): void {
    $this->listen(CoreHookEvents::MAIL_ALTER, 'onMailAlter');

    $message = $this->container->get('plugin.manager.mail')->doMail('system', 'key', 'admin@example.com', 'en');
    $this->assertFalse($message['send']);
  }

  /**
   * Callback for MailAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\MailAlterEvent $event
   *   The event.
   */
  public function onMailAlter(MailAlterEvent $event): void {
    $message = &$event->getMessage();
    $message['send'] = FALSE;
  }

}
