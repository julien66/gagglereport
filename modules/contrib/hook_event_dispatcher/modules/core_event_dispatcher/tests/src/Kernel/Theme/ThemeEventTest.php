<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\core_event_dispatcher\Event\Theme\ThemeEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\ThemeEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ThemeEventTest extends KernelTestBase {

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
   * Theme path.
   *
   * @var string|null
   */
  protected $path;

  /**
   * ThemeEvent with addNewThemes test.
   *
   * @dataProvider themeEventProvider
   *
   * @throws \Exception
   */
  public function testThemeEvent(?string $path): void {
    if (!$path) {
      $this->expectException(\RuntimeException::class);
      $this->expectExceptionMessage('Missing path in the information array. ThemeEvent needs the path to be set manually, to have a proper default theme implementation. See \hook_theme() for more information.');
    }

    $this->path = $path;
    $this->listen(ThemeHookEvents::THEME, 'onTheme');
    $registry = $this->container->get('theme.registry')->get();
    $this->assertArrayHasKey('some_custom__hook_theme', $registry);
  }

  /**
   * Callback for ThemeEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\ThemeEvent $event
   *   The event.
   */
  public function onTheme(ThemeEvent $event): void {
    $this->assertArrayHasKey('theme_test_foo', $event->getExisting());
    $event->addNewThemes([
      'some_custom__hook_theme' => [
        'variables' => [
          'custom_variable' => NULL,
        ],
        'path' => $this->path,
      ],
    ]);
  }

  /**
   * Data provider for testThemeEvent.
   *
   * @return array
   *   The provided data.
   */
  public static function themeEventProvider(): array {
    return [
      [NULL],
      ['some/path'],
    ];
  }

}
