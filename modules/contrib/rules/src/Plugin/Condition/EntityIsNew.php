<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;

/**
 * Provides an 'Entity is new' condition.
 *
 * @todo Add access callback information from Drupal 7?
 *
 * @Condition(
 *   id = "rules_entity_is_new",
 *   label = @Translation("Entity is new"),
 *   category = @Translation("Entity"),
 *   context_definitions = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_entity_is_new",
  label: new TranslatableMarkup("Entity is new"),
  category: new TranslatableMarkup("Entity"),
  context_definitions: [
    "entity" => new ContextDefinition(
      data_type: "entity",
      label: new TranslatableMarkup("Entity"),
      description: new TranslatableMarkup("Specifies the entity for which to evaluate the condition."),
      assignment_restriction: "selector"
    ),
  ]
)]
class EntityIsNew extends RulesConditionBase {

  /**
   * Check if the provided entity is new.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check.
   *
   * @return bool
   *   TRUE if the provided entity is new.
   */
  protected function doEvaluate(EntityInterface $entity) {
    return $entity->isNew();
  }

}
