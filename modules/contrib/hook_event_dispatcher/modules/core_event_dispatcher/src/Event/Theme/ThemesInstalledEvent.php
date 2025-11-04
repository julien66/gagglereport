<?php

namespace Drupal\core_event_dispatcher\Event\Theme;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class ThemesInstalledEvent.
 */
#[HookEvent(id: 'themes_installed', hook: 'themes_installed')]
class ThemesInstalledEvent extends Event implements EventInterface {

  /**
   * ThemesInstalledEvent constructor.
   *
   * @param array $themeList
   *   Array containing the names of the themes being installed.
   */
  public function __construct(private readonly array $themeList) {
  }

  /**
   * Get theme list.
   *
   * @return array
   *   Theme list.
   */
  public function getThemeList(): array {
    return $this->themeList;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ThemeHookEvents::THEMES_INSTALLED;
  }

}
