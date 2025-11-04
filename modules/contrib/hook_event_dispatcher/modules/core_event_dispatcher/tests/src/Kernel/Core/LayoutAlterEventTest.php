<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\LayoutAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class LayoutAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\LayoutAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class LayoutAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'layout_discovery',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the LayoutAlterEvent.
   *
   * @throws \Exception
   */
  public function testLayoutAlter(): void {
    $this->listen(CoreHookEvents::LAYOUT_ALTER, 'onLayoutAlter');

    $definitions = $this->container->get('plugin.manager.core.layout')->getDefinitions();

    $this->assertArrayHasKey('test', $definitions);
    $this->assertArrayNotHasKey('layout_twocol', $definitions);
  }

  /**
   * Callback for LayoutAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\LayoutAlterEvent $event
   *   The event.
   */
  public function onLayoutAlter(LayoutAlterEvent $event): void {
    $definitions = $event->getDefinitions();

    $this->assertArrayNotHasKey('test', $definitions);
    $event->setDefinition('test', $definitions['layout_onecol']);

    $this->assertArrayHasKey('layout_twocol', $definitions);
    $event->deleteDefinition('layout_twocol');
  }

}
