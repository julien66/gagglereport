<?php

namespace Drupal\rules\Core;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\CategorizingPluginManagerTrait;
use Drupal\Core\Plugin\Context\ContextAwarePluginManagerTrait;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\AttributeClassDiscovery;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\rules\Context\AnnotatedClassDiscovery as RulesAnnotatedClassDiscovery;
use Drupal\rules\Context\AttributeDiscoveryWithAnnotations as RulesAttributeDiscoveryWithAnnotations;
use Drupal\rules\Core\Attribute\RulesAction;

/**
 * Provides a RulesAction plugin manager for the Rules actions API.
 *
 * @see \Drupal\rules\Core\Annotation\RulesAction
 * @see \Drupal\rules\Core\Attribute\RulesAction
 * @see \Drupal\rules\Core\RulesActionInterface
 * @see \Drupal\rules\Core\RulesActionBase
 * @see plugin_api
 */
class RulesActionManager extends DefaultPluginManager implements RulesActionManagerInterface {
  use CategorizingPluginManagerTrait;
  use ContextAwarePluginManagerTrait;

  /**
   * Constructs a new class instance.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/RulesAction', $namespaces, $module_handler, RulesActionInterface::class, RulesAction::class, '\Drupal\rules\Core\Annotation\RulesAction');
    $this->alterInfo('rules_action_info');
    $this->setCacheBackend($cache_backend, 'rules_action_plugins');
  }

  /**
   * {@inheritdoc}
   *
   * @todo This method changes DefaultPluginManager::getDiscovery() only for
   * the case when Annotation-based discovery is used. This entire method
   * should be removed when Annotation-based discovery is no longer supported,
   * so that this RulesActionManager will revert to using the parent method
   * DefaultPluginManager::getDiscovery().
   */
  protected function getDiscovery() {
    if (!$this->discovery) {
      if (isset($this->pluginDefinitionAttributeName) && isset($this->pluginDefinitionAnnotationName)) {
        // If both are given, we need to discover plugins based on both
        // Attributes *and* Annotations, for backwards compatibility.
        //
        // For Rules we need to swap out the annotated class discovery used, so
        // we can control the annotation classes picked. Specifically needed
        // for ContextDefinition.
        /** @var \Drupal\rules\Context\AttributeDiscoveryWithAnnotations $discovery */
        $discovery = new RulesAttributeDiscoveryWithAnnotations($this->subdir, $this->namespaces, $this->pluginDefinitionAttributeName, $this->pluginDefinitionAnnotationName, $this->additionalAnnotationNamespaces);
      }
      elseif (isset($this->pluginDefinitionAttributeName)) {
        // Discover plugins solely by Attributes.
        //
        // This does not need to be overridden for Rules, because the
        // ContextDefinition class used is not specified by the core Attribute
        // class, like it is in the core Annotation discovery. Thus we can use
        // our own ContextDefinition without overriding plugin discovery.
        $discovery = new AttributeClassDiscovery($this->subdir, $this->namespaces, $this->pluginDefinitionAttributeName);
      }
      else {
        // Discover plugins solely by Annotations.
        //
        // For Rules we need to swap out the annotated class discovery used, so
        // we can control the annotation classes picked. Specifically needed
        // for ContextDefinition.
        /** @var \Drupal\rules\Context\AnnotatedClassDiscovery $discovery */
        $discovery = new RulesAnnotatedClassDiscovery($this->subdir, $this->namespaces, $this->pluginDefinitionAnnotationName);
      }
      // In all cases, decorate the discovery.
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($discovery);
    }
    return $this->discovery;
  }

}
