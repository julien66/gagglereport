<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\core_event_dispatcher\Event\Theme\AbstractThemeSuggestionsEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\AbstractThemeSuggestionsEvent
 * @covers \Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterEvent
 * @covers \Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterIdEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ThemeSuggestionsAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  private const THEME_HOOK = 'theme_test_preprocess_suggestions';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'theme_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Tests the themeSuggestionsAlterEvent.
   *
   * @throws \Exception
   */
  public function testThemeSuggestionsAlterEvent(): void {
    $this->listen([
      ThemeHookEvents::THEME_SUGGESTIONS_ALTER,
      'hook_event_dispatcher.theme.suggestions_' . self::THEME_HOOK . '_alter',
    ], 'onThemeSuggestionsAlter', $this->exactly(2));

    $render = $this->container->get('theme.manager')->render(self::THEME_HOOK, ['foo' => 'test']);
    $this->assertStringContainsString('Template for testing specific theme calls.', $render);
  }

  /**
   * Callback for ThemeSuggestionsAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\AbstractThemeSuggestionsEvent $event
   *   The event.
   */
  public function onThemeSuggestionsAlter(AbstractThemeSuggestionsEvent $event): void {
    $suggestions = &$event->getSuggestions();
    $suggestions[] = 'theme_test_specific_suggestions';

    $variables = $event->getVariables();
    $this->assertEquals('test', $variables['foo']);
    $this->assertEquals(self::THEME_HOOK, $event->getHook());
  }

}
