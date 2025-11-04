<?php

namespace Drupal\rules\Context;

use Drupal\Core\Plugin\Context\EntityContextDefinition as CoreEntityContextDefinition;
use Drupal\Component\Plugin\Exception\ContextException;

/**
 * Extends the core entity context definition class with useful methods.
 *
 * Warning: Do not instantiate this directly in your code. This class is only
 * meant to be used from \Drupal\rules\Context\ContextDefinition. Please read
 * the API documentation for that class and see the links below for details.
 *
 * @see \Drupal\rules\Context\ContextDefinition
 * @see https://www.drupal.org/project/rules/issues/3161582
 * @see https://www.drupal.org/project/drupal/issues/3126747
 *
 * @internal
 */
class EntityContextDefinition extends CoreEntityContextDefinition implements ContextDefinitionInterface {
  use RulesContextDefinitionTrait;

  /**
   * {@inheritdoc}
   */
  public function toArray(): array {
    $values = [];
    $defaults = get_class_vars(__CLASS__);
    // This is \Drupal\rules\Context\ContextDefinition.
    foreach (static::$nameMap as $key => $property_name) {
      // Only export values for non-default properties.
      if ($this->$property_name !== $defaults[$property_name]) {
        $values[$key] = $this->$property_name;
      }
    }
    return $values;
  }

  /**
   * Creates a definition object from an exported array of values.
   *
   * @param array $values
   *   The array of values, as returned by toArray().
   *
   * @return static
   *   The created definition.
   *
   * @throws \Drupal\Component\Plugin\Exception\ContextException
   *   If the required classes are not implemented.
   */
  public static function createFromArray(array $values) {
    if (isset($values['class']) && !in_array(ContextDefinitionInterface::class, class_implements($values['class']))) {
      throw new ContextException('EntityContextDefinition class must implement ' . ContextDefinitionInterface::class . '.');
    }
    // Default to Rules context definition class.
    $values['class'] = $values['class'] ?? EntityContextDefinition::class;
    if (!isset($values['value'])) {
      $values['value'] = 'any';
    }

    $definition = $values['class']::create($values['value']);
    // This is \Drupal\rules\Context\ContextDefinition.
    foreach (array_intersect_key(static::$nameMap, $values) as $key => $name) {
      $definition->$name = $values[$key];
    }
    return $definition;
  }

}
