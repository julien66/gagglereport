<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\TypedData\Options\FieldListOptions;
use Drupal\rules\TypedData\Options\ViewEditOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'User has entity field access' condition.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @Condition(
 *   id = "rules_entity_field_access",
 *   label = @Translation("User has entity field access"),
 *   category = @Translation("User"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user account for which to check access. If left empty, the currently logged in user will be used."),
 *       assignment_restriction = "selector",
 *       required = FALSE
 *     ),
 *     "entity" = @ContextDefinition("entity",
 *       label = @Translation("Entity"),
 *       description = @Translation("Specifies the entity for which to evaluate the condition."),
 *       assignment_restriction = "selector"
 *     ),
 *     "field" = @ContextDefinition("string",
 *       label = @Translation("Field"),
 *       description = @Translation("The name of the field to check for."),
 *       assignment_restriction = "input",
 *       options_provider = "\Drupal\rules\TypedData\Options\FieldListOptions"
 *     ),
 *     "operation" = @ContextDefinition("string",
 *       label = @Translation("Access operation"),
 *       description = @Translation("The access type to check."),
 *       assignment_restriction = "input",
 *       default_value = "view",
 *       required = FALSE,
 *       options_provider = "\Drupal\rules\TypedData\Options\ViewEditOptions"
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_entity_field_access",
  label: new TranslatableMarkup("User has entity field access"),
  category: new TranslatableMarkup("User"),
  context_definitions: [
    "user" => new ContextDefinition(
      data_type: "entity:user",
      label: new TranslatableMarkup("User"),
      description: new TranslatableMarkup("Specifies the user account for which to check access. If left empty, the currently logged in user will be used."),
      assignment_restriction: "selector",
      required: FALSE
    ),
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
      assignment_restriction: "input",
      options_provider: FieldListOptions::class
    ),
    "operation" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Access operation"),
      description: new TranslatableMarkup("The access type to check."),
      assignment_restriction: "input",
      default_value: "view",
      required: FALSE,
      options_provider: ViewEditOptions::class
    ),
  ]
)]
class UserHasEntityFieldAccess extends RulesConditionBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a UserHasEntityFieldAccess object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity_type.manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * Evaluate if the user has access to the field of an entity.
   *
   * @param \Drupal\Core\Session\AccountInterface $user
   *   The user account to test access against.
   * @param \Drupal\Core\Entity\ContentEntityInterface $entity
   *   The entity to check access on.
   * @param string $field
   *   The name of the field to check access on.
   * @param string $operation
   *   The operation access should be checked for. Usually one of "view" or
   *   "edit".
   *
   * @return bool
   *   TRUE if the user has access to the field on the entity, FALSE otherwise.
   */
  protected function doEvaluate(AccountInterface $user, ContentEntityInterface $entity, $field, $operation) {
    if (!$entity->hasField($field)) {
      return FALSE;
    }

    $access = $this->entityTypeManager->getAccessControlHandler($entity->getEntityTypeId());
    if (!$access->access($entity, $operation, $user)) {
      return FALSE;
    }

    $definition = $entity->getFieldDefinition($field);
    $items = $entity->get($field);
    return $access->fieldAccess($operation, $definition, $user, $items);
  }

}
