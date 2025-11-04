<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\TypedData\Options\FieldListOptions;

/**
 * Provides a 'Entity has field' condition.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @Condition(
 *   id = "rules_entity_has_field",
 *   label = @Translation("Entity has field"),
 *   category = @Translation("Entity"),
 *   context_definitions = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *     "field" = @ContextDefinition("string",
 *       label = @Translation("Field"),
 *       description = @Translation("The name of the field to check for."),
 *       options_provider = "\Drupal\rules\TypedData\Options\FieldListOptions",
 *       assignment_restriction = "input"
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_entity_has_field",
  label: new TranslatableMarkup("Entity has field"),
  category: new TranslatableMarkup("Entity"),
  context_definitions: [
    "entity" => new ContextDefinition(
      data_type: "entity",
      label: new TranslatableMarkup("Entity"),
      description: new TranslatableMarkup("Specifies the entity for which to evaluate the condition."),
      assignment_restriction: "selector"
    ),
    "field" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Field"),
      description: new TranslatableMarkup("The name of the field to check for."),
      options_provider: FieldListOptions::class,
      assignment_restriction: "input"
    ),
  ]
)]
class EntityHasField extends RulesConditionBase {

  /**
   * Checks if a given entity has a given field.
   *
   * @param \Drupal\Core\Entity\FieldableEntityInterface $entity
   *   The entity to check for the provided field.
   * @param string $field
   *   The field to check for on the entity.
   *
   * @return bool
   *   TRUE if the provided entity has the provided field.
   */
  protected function doEvaluate(FieldableEntityInterface $entity, $field) {
    return $entity->hasField($field);
  }

}
