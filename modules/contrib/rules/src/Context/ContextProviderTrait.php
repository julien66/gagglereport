<?php

namespace Drupal\rules\Context;

use Drupal\Component\Plugin\Exception\ContextException;
use Drupal\Core\Plugin\Context\Context;
use Symfony\Component\Routing\Route;

/**
 * A trait implementing the ContextProviderInterface.
 *
 * This trait is intended for context aware plugins that want to provide
 * context.
 *
 * The trait requires the plugin to use configuration as defined by the
 * ContextConfig class.
 *
 * @see \Drupal\rules\Context\ContextProviderInterface
 */
trait ContextProviderTrait {

  /**
   * The data objects that are provided by this plugin.
   *
   * @var \Drupal\Component\Plugin\Context\ContextInterface[]
   */
  protected $providedContext;

  /**
   * @see \Drupal\rules\Context\ContextProviderInterface
   */
  public function setProvidedValue($name, $value) {
    $context = $this->getProvidedContext($name);
    $new_context = Context::createFromContext($context, $value);
    $this->providedContext[$name] = $new_context;
    return $this;
  }

  /**
   * @see \Drupal\rules\Context\ContextProviderInterface
   */
  public function getProvidedContext($name) {
    // Check for a valid context value.
    if (!isset($this->providedContext[$name])) {
      $this->providedContext[$name] = new Context($this->getProvidedContextDefinition($name));
    }
    return $this->providedContext[$name];
  }

  /**
   * @see \Drupal\rules\Context\ContextProviderInterface
   */
  public function getProvidedContextDefinition($name) {
    $definition = $this->getPluginDefinition();
    if (empty($definition['provides'][$name])) {
      throw new ContextException(sprintf("The provided context '%s' is not valid.", $name));
    }
    return $definition['provides'][$name];
  }

  /**
   * @see \Drupal\rules\Context\ContextProviderInterface
   */
  public function getProvidedContextDefinitions() {
    $definition = $this->getPluginDefinition();
    return !empty($definition['provides']) ? $definition['provides'] : [];
  }

  /**
   * Upcasts an entity id to a full entity object.
   *
   * Returns the entity object if the upcast was successful, otherwise returns
   * NULL.
   *
   * @todo Rather than returning NULL, we should probably throw an exception.
   * That way the calling code may attempt an upcast, then continue on as it
   * used to if the upcast fails.
   *
   * @param string $id
   *   The unique entity id to upcast to a full entity.
   * @param string $type
   *   The entity data type. For example, "entity:node".
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   The upcasted entity object (if successful) or null (if not).
   */
  public function upcastEntityId($id, $type) {
    // If the passed value is (accidentally) already an object, just return it.
    if (is_object($id)) {
      return $id;
    }
    $paramConverterManager = \Drupal::service('paramconverter_manager');
    /** @var \Drupal\Core\ParamConverter\ParamConverterInterface $param_converter */
    $param_converter = $paramConverterManager->getConverter('paramconverter.entity');

    // The $name variable is just an arbitrary slug for use in the route object.
    $name = 'id_to_upcast';

    // The $definition variable declares what datatype the slug represents.
    $definition = ['type' => $type];

    // The Route class used here is just a data structure for holding data
    // necessary for a route definition. Creating an object of this type does
    // not in any way affect routing on the site. We only use Route here because
    // the paramconverter_manager requires this structure for one of its inputs.
    $route = new Route('/{$name}');

    // Check that the definition can be upcast and if so do it.
    if ($param_converter->applies($definition, $name, $route)) {
      $defaults = [$name => $id];
      $upcasted_object = $param_converter->convert(strtolower($id), $definition, $name, $defaults);
      return $upcasted_object;
    }
    else {
      return NULL;
    }
  }

}
