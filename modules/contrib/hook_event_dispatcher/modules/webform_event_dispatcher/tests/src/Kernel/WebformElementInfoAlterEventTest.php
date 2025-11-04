<?php

namespace Drupal\Tests\webform_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementInfoAlterEvent;
use Drupal\webform_event_dispatcher\WebformHookEvents;

/**
 * Class WebformElementInfoAlterEventTest.
 *
 * @covers \Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementInfoAlterEvent
 *
 * @requires module webform
 * @group hook_event_dispatcher
 * @group webform_event_dispatcher
 */
class WebformElementInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'webform',
    'hook_event_dispatcher',
    'webform_event_dispatcher',
  ];

  /**
   * Test WebformElementInfoAlterEvent.
   *
   * @throws \Exception
   */
  public function testWebformElementInfoAlterEvent(): void {
    $this->listen(WebformHookEvents::WEBFORM_ELEMENT_INFO_ALTER, 'onWebformElementInfoAlter');
    $definitions = $this->container->get('plugin.manager.webform.element')->getDefinitions();

    $this->assertArrayHasKey('#test', $definitions['textfield']);
    $this->assertEquals('test', $definitions['textfield']['#test']);
  }

  /**
   * Callback for WebformElementInfoAlterEvent.
   *
   * @param \Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementInfoAlterEvent $event
   *   The event.
   */
  public function onWebformElementInfoAlter(WebformElementInfoAlterEvent $event): void {
    $definitions = &$event->getDefinitions();
    $definitions['textfield']['#test'] = 'test';
  }

}
