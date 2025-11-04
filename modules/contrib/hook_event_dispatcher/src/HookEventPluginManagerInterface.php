<?php

namespace Drupal\hook_event_dispatcher;

/**
 * Interface HookEventPluginManagerInterface.
 */
interface HookEventPluginManagerInterface {

  /**
   * Generates callables which generate hook events from arguments.
   *
   * @param string $hook
   *   A hook, without 'hook_' prefix.
   *
   * @return \Generator|callable[]
   *   Events.
   */
  public function getHookEventFactories(string $hook): \Generator;

  /**
   * Generates callables which generate alter events from arguments.
   *
   * @param string $alter
   *   An alter, without 'hook_' prefix and '_alter' suffix.
   *
   * @return \Generator|callable[]
   *   Events.
   */
  public function getAlterEventFactories(string $alter): \Generator;

}
