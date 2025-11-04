<?php

namespace Drupal\rules\Core;

use Drupal\Core\Condition\ConditionManager as CoreConditionManager;
use Drupal\Core\Plugin\Discovery\AttributeClassDiscovery;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\rules\Context\AnnotatedClassDiscovery as RulesAnnotatedClassDiscovery;
use Drupal\rules\Context\AttributeDiscoveryWithAnnotations as RulesAttributeDiscoveryWithAnnotations;

/**
 * Extends the core condition manager to add in Rules' context improvements.
 *
 * @see \Drupal\rules\Core\Annotation\Condition
 * @see \Drupal\rules\Core\Attribute\Condition
 * @see \Drupal\rules\Core\RulesConditionInterface
 * @see \Drupal\rules\Core\RulesConditionBase
 * @see plugin_api
 */
class ConditionManager extends CoreConditionManager {

  /**
   * {@inheritdoc}
   *
   * @return \Drupal\rules\Core\RulesConditionInterface|\Drupal\Core\Condition\ConditionInterface
   *   A fully configured plugin instance.
   */
  public function createInstance($plugin_id, array $configuration = []) {
    return parent::createInstance($plugin_id, $configuration);
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

  /**
   * {@inheritdoc}
   */
  public function getDefinitions() {
    $definitions = parent::getDefinitions();
    // Make sure that all definitions have a category to avoid PHP notices in
    // CategorizingPluginManagerTrait.
    // @todo Fix this in core in CategorizingPluginManagerTrait.
    foreach ($definitions as &$definition) {
      if (!isset($definition['category'])) {
        $definition['category'] = $this->t('Other');
      }
    }
    return $definitions;
  }

}
