<?php

namespace Drupal\Tests\toolbar_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\toolbar\Element\Toolbar;
use Drupal\toolbar_event_dispatcher\Event\Toolbar\ToolbarAlterEvent;
use Drupal\toolbar_event_dispatcher\ToolbarHookEvents;

/**
 * Class ToolbarAlterEventTest.
 *
 * @covers \Drupal\toolbar_event_dispatcher\Event\Toolbar\ToolbarAlterEvent
 * @covers \Drupal\toolbar_event_dispatcher\Event\Toolbar\ToolbarEvent
 *
 * @group hook_event_dispatcher
 * @group toolbar_event_dispatcher
 */
class ToolbarEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'breakpoint',
    'toolbar',
    'user',
    'hook_event_dispatcher',
    'toolbar_event_dispatcher',
  ];

  /**
   * Test the ToolbarEvent and ToolbarAlterEvent.
   *
   * @throws \Exception
   */
  public function testToolbarAlterEvent(): void {
    $this->listen(ToolbarHookEvents::TOOLBAR, 'onToolbar');
    $this->listen(ToolbarHookEvents::TOOLBAR_ALTER, 'onToolbarAlter');

    $toolbar = Toolbar::preRenderToolbar([]);
    $this->assertArrayHasKey('test', $toolbar);
    $this->assertContains('item', $toolbar['test']);
  }

  /**
   * Callback for ToolbarEvent.
   */
  public function onToolbar(): void {
  }

  /**
   * Callback for ToolbarAlterEvent.
   *
   * @param \Drupal\toolbar_event_dispatcher\Event\Toolbar\ToolbarAlterEvent $event
   *   The event.
   */
  public function onToolbarAlter(ToolbarAlterEvent $event): void {
    $items = &$event->getItems();
    $items['test'] = ['item'];
  }

}
