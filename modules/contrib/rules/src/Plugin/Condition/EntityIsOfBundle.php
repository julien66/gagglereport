<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\TypedData\Options\EntityBundleOptions;
use Drupal\rules\TypedData\Options\EntityTypeOptions;

/**
 * Provides an 'Entity is of bundle' condition.
 *
 * @todo Add access callback information from Drupal 7?
 *
 * @Condition(
 *   id = "rules_entity_is_of_bundle",
 *   label = @Translation("Entity is of bundle"),
 *   category = @Translation("Entity"),
 *   context_definitions = {
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *     "type" = @ContextDefinition("string",
 *       label = @Translation("Type"),
 *       description = @Translation("The type of the evaluated entity."),
 *       options_provider = "\Drupal\rules\TypedData\Options\EntityTypeOptions",
 *       assignment_restriction = "input"
 *     ),
 *     "bundle" = @ContextDefinition("string",
 *       label = @Translation("Bundle"),
 *       description = @Translation("The bundle of the evaluated entity."),
 *       options_provider = "\Drupal\rules\TypedData\Options\EntityBundleOptions",
 *       assignment_restriction = "input"
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_entity_is_of_bundle",
  label: new TranslatableMarkup("Entity is of bundle"),
  category: new TranslatableMarkup("Entity"),
  context_definitions: [
    "entity" => new ContextDefinition(
      data_type: "entity",
      label: new TranslatableMarkup("Entity"),
      description: new TranslatableMarkup("Specifies the entity for which to evaluate the condition."),
      assignment_restriction: "selector"
    ),
    "type" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Type"),
      description: new TranslatableMarkup("The type of the evaluated entity."),
      options_provider: EntityTypeOptions::class,
      assignment_restriction: "input"
    ),
    "bundle" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Bundle"),
      description: new TranslatableMarkup("The bundle of the evaluated entity."),
      options_provider: EntityBundleOptions::class,
      assignment_restriction: "input"
    ),
  ]
)]
class EntityIsOfBundle extends RulesConditionBase {

  /**
   * Check if a provided entity is of a specific type and bundle.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to check the bundle and type of.
   * @param string $type
   *   The type to check for.
   * @param string $bundle
   *   The bundle to check for.
   *
   * @return bool
   *   TRUE if the provided entity is of the provided type and bundle.
   */
  protected function doEvaluate(EntityInterface $entity, $type, $bundle) {
    $entity_type = $entity->getEntityTypeId();
    $entity_bundle = $entity->bundle();

    // Check to see whether the entity's bundle and type match the specified
    // values.
    return $entity_bundle == $bundle && $entity_type == $type;
  }

  /**
   * {@inheritdoc}
   */
  public function assertMetadata(array $selected_data) {
    // Assert the checked bundle.
    $changed_definitions = [];
    if (isset($selected_data['entity']) && $bundle = $this->getContextValue('bundle')) {
      $changed_definitions['entity'] = clone $selected_data['entity'];
      $bundles = is_array($bundle) ? $bundle : [$bundle];
      $changed_definitions['entity']->setBundles($bundles);
    }
    return $changed_definitions;
  }

}
