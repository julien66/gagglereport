<?php

declare(strict_types=1);

namespace Drupal\hook_event_dispatcher\Plugin\Factory;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\hook_event_dispatcher\Event\EventFactoryInterface;

/**
 * Factory for hook event plugins.
 */
class EventFactory extends DefaultFactory {

  /**
   * {@inheritdoc}
   */
  public function createInstance($pluginId, array $configuration = []) {
    $pluginDefinition = $this->discovery->getDefinition($pluginId);
    $pluginClass = static::getPluginClass($pluginId, $pluginDefinition, $this->interface);

    if (is_subclass_of($pluginClass, EventFactoryInterface::class)) {
      return $pluginClass::create(...$configuration);
    }

    return new $pluginClass(...$configuration);
  }

}
