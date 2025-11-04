<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for theme hooks.
 */
final class ThemeHookEvents {

  /**
   * Register a module or theme's theme implementations.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\ThemeEvent
   * @see core_event_dispatcher_theme()
   * @see hook_theme()
   *
   * @var string
   */
  public const THEME = HookEventDispatcherInterface::PREFIX . 'theme';

  /**
   * Alter the theme registry information returned from hook_theme().
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\ThemeRegistryAlterEvent
   * @see core_event_dispatcher_theme_registry_alter()
   * @see hook_theme_registry_alter()
   *
   * @var string
   */
  public const THEME_REGISTRY_ALTER = HookEventDispatcherInterface::PREFIX . 'theme.registry_alter';

  /**
   * Alters named suggestions for all theme hooks.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\ThemeSuggestionsAlterEvent
   * @see core_event_dispatcher_theme_suggestions_alter()
   * @see hook_theme_suggestions_alter()
   *
   * @var string
   */
  public const THEME_SUGGESTIONS_ALTER = HookEventDispatcherInterface::PREFIX . 'theme.suggestions_alter';

  /**
   * Respond to themes being installed.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\ThemesInstalledEvent
   * @see core_event_dispatcher_themes_installed()
   * @see hook_themes_installed()
   *
   * @var string
   */
  public const THEMES_INSTALLED = HookEventDispatcherInterface::PREFIX . 'theme.installed';

  /**
   * Alter the default, hook-independent variables for all templates.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\TemplatePreprocessDefaultVariablesAlterEvent
   * @see core_event_dispatcher_template_preprocess_default_variables_alter()
   * @see hook_template_preprocess_default_variables_alter()
   *
   * @var string
   */
  public const TEMPLATE_PREPROCESS_DEFAULT_VARIABLES_ALTER = HookEventDispatcherInterface::PREFIX . 'theme.template_preprocess_default_variables_alter';

  /**
   * Perform necessary alterations to the JS before it is presented on the page.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\JsAlterEvent
   * @see core_event_dispatcher_js_alter()
   * @see hook_js_alter()
   *
   * @var string
   */
  public const JS_ALTER = HookEventDispatcherInterface::PREFIX . 'js.alter';

  /**
   * Alter the library info provided by an extension.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\LibraryInfoAlterEvent
   * @see core_event_dispatcher_library_info_alter()
   * @see hook_library_info_alter()
   *
   * @var string
   */
  public const LIBRARY_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'library.info_alter';

}
