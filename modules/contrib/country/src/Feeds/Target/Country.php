<?php

namespace Drupal\country\Feeds\Target;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\feeds\FieldTargetDefinition;
use Drupal\feeds\Plugin\Type\Target\FieldTargetBase;

/**
 * Defines a field mapper for Country Field.
 *
 * @FeedsTarget(
 *   id = "country",
 *   field_types = {"address_country"}
 * )
 */
class Country extends FieldTargetBase {

  /**
   * {@inheritdoc}
   */
  protected static function prepareTarget(FieldDefinitionInterface $field_definition) {
    $definition = FieldTargetDefinition::createFromFieldDefinition($field_definition)
      ->addProperty('value');

    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  protected function prepareValue($delta, array &$values) {
    $value = mb_strtoupper(trim($values['value']));
    $values['value'] = $value;
  }

}
