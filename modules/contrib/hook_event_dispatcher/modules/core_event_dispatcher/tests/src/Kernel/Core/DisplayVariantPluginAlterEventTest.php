<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\DisplayVariantPluginAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class DisplayVariantPluginAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\DisplayVariantPluginAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class DisplayVariantPluginAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Tests the DisplayVariantPluginAlterEvent.
   *
   * @throws \Exception
   */
  public function testDisplayVariantPluginAlter(): void {
    $this->listen(CoreHookEvents::DISPLAY_VARIANT_PLUGIN_ALTER, 'onDisplayVariantPluginAlter');

    $definitions = $this->container->get('plugin.manager.display_variant')->getDefinitions();
    $this->assertEquals('Altered', $definitions['simple_page']['admin_label']);
  }

  /**
   * Callback for DisplayVariantPluginAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\DisplayVariantPluginAlterEvent $event
   *   The event.
   */
  public function onDisplayVariantPluginAlter(DisplayVariantPluginAlterEvent $event): void {
    $definitions = &$event->getDefinitions();
    $this->assertArrayHasKey('simple_page', $definitions);
    $this->assertArrayHasKey('admin_label', $definitions['simple_page']);
    $this->assertEquals('Simple page', $definitions['simple_page']['admin_label']);

    $definitions['simple_page']['admin_label'] = 'Altered';
  }

}
