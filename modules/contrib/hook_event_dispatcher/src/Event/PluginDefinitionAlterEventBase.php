<?php

namespace Drupal\hook_event_dispatcher\Event;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Component\Plugin\Definition\PluginDefinitionInterface;
use Drupal\Component\Plugin\Discovery\DiscoveryCachedTrait;
use Drupal\Component\Plugin\Discovery\DiscoveryInterface;

/**
 * Class PluginDefinitionAlterEventBase.
 */
abstract class PluginDefinitionAlterEventBase extends Event implements EventInterface, DiscoveryInterface {

  use DiscoveryCachedTrait;

  /**
   * PluginDefinitionAlterEventBase constructor.
   *
   * @param \Drupal\Component\Plugin\Definition\PluginDefinition[] $definitions
   *   The array of definitions, keyed by plugin ID.
   */
  public function __construct(array &$definitions) {
    $this->definitions = &$definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function &getDefinitions(): array {
    return $this->definitions;
  }

  /**
   * Sets a plugin definition.
   */
  public function setDefinition(string $plugin, array|PluginDefinitionInterface $definition): void {
    $this->definitions[$plugin] = $definition;
  }

  /**
   * Deletes a plugin definition.
   */
  public function deleteDefinition(string $plugin): void {
    unset($this->definitions[$plugin]);
  }

}
