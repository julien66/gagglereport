<?php

namespace Drupal\rules\Context;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\rules\Attribute\RulesDataProcessor;

/**
 * Plugin manager for Rules data processors.
 *
 * @see \Drupal\rules\Context\DataProcessorInterface
 */
class DataProcessorManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/RulesDataProcessor', $namespaces, $module_handler, DataProcessorInterface::class, RulesDataProcessor::class, '\Drupal\rules\Annotation\RulesDataProcessor');
    $this->alterInfo('rules_data_processor_info');
    $this->setCacheBackend($cache_backend, 'rules_data_processor_plugins');
  }

}
