<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\core_event_dispatcher\Event\Theme\ThemeRegistryAlterEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\ThemeRegistryAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ThemeRegistryAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'theme_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * ThemeRegistryAlterEvent with theme implementation alter test.
   *
   * @throws \Exception
   */
  public function testThemeRegistryAlterEventWithThemeAlter(): void {
    $this->listen(ThemeHookEvents::THEME_REGISTRY_ALTER, 'onThemeRegistryAlter');

    $registry = $this->container->get('theme.registry')->get();
    $this->assertArrayHasKey('bar', $registry['theme_test']['variables']);
  }

  /**
   * Callback for ThemeRegistryAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\ThemeRegistryAlterEvent $event
   *   The event.
   */
  public function onThemeRegistryAlter(ThemeRegistryAlterEvent $event): void {
    $registry = &$event->getThemeRegistry();
    $this->assertArrayNotHasKey('bar', $registry['theme_test']['variables']);
    $registry['theme_test']['variables']['bar'] = '';
  }

}
