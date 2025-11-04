<?php

namespace Drupal\core_event_dispatcher\Event\Theme;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class ThemeRegistryAlterEvent.
 */
#[HookEvent(id: 'theme_registry_alter', alter: 'theme_registry')]
class ThemeRegistryAlterEvent extends Event implements EventInterface {

  /**
   * The entire cache of theme registry information, post-processing.
   *
   * @var array
   */
  private array $themeRegistry = [];

  /**
   * ThemeRegistryAlterEvent constructor.
   *
   * @param array &$themeRegistry
   *   The entire cache of theme registry information, post-processing.
   */
  public function __construct(array &$themeRegistry) {
    $this->themeRegistry = &$themeRegistry;
  }

  /**
   * Get theme registry.
   *
   * @return array
   *   The theme registry.
   */
  public function &getThemeRegistry(): array {
    return $this->themeRegistry;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ThemeHookEvents::THEME_REGISTRY_ALTER;
  }

}
