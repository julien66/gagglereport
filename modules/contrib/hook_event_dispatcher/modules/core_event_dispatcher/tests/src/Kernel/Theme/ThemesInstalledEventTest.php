<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\core_event_dispatcher\Event\Theme\ThemesInstalledEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\ThemesInstalledEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ThemesInstalledEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Themes installed event test.
   *
   * @throws \Exception
   */
  public function testThemesInstalledEvent(): void {
    $this->listen(ThemeHookEvents::THEMES_INSTALLED, 'onThemesInstalled');
    $this->assertTrue($this->container->get('theme_installer')->install(['test_theme']));
  }

  /**
   * Callback for ThemesInstalledEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\ThemesInstalledEvent $event
   *   The event.
   */
  public function onThemesInstalled(ThemesInstalledEvent $event): void {
    $this->assertEquals([
      'stable9',
      'starterkit_theme',
      'test_theme',
    ], $event->getThemeList());
  }

}
