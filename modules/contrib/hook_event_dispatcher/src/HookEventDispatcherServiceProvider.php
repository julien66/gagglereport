<?php

declare(strict_types=1);

namespace Drupal\hook_event_dispatcher;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Defines a service provider for the Hook Event Dispatcher module.
 *
 * @see https://www.drupal.org/node/2026959
 */
final class HookEventDispatcherServiceProvider implements ServiceModifierInterface {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container): void {
    if ($container->hasDefinition('plugin.manager.tracer')) {
      $container->getDefinition('plugin.manager.tracer')
        ->replaceArgument(2, new Reference('hook_event_dispatcher.module_handler.inner'));
    }
  }

}
