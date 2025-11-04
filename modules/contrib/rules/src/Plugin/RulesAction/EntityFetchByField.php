<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;
use Drupal\rules\TypedData\Options\EntityTypeOptions;
use Drupal\rules\TypedData\Options\FieldListOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Fetch entities by field' action.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @RulesAction(
 *   id = "rules_entity_fetch_by_field",
 *   label = @Translation("Fetch entities by field"),
 *   category = @Translation("Entity"),
 *   context_definitions = {
 *     "type" = @ContextDefinition("string",
 *       label = @Translation("Entity type"),
 *       description = @Translation("Specify the type of the entity that should be fetched."),
 *       options_provider = "\Drupal\rules\TypedData\Options\EntityTypeOptions",
 *     ),
 *     "field_name" = @ContextDefinition("string",
 *       label = @Translation("Field name"),
 *       description = @Translation("Name of the field by which the entity is to be selected."),
 *       options_provider = "\Drupal\rules\TypedData\Options\FieldListOptions",
 *     ),
 *     "field_value" = @ContextDefinition("any",
 *       label = @Translation("Field value"),
 *       description = @Translation("The field value of the entity to be fetched.")
 *     ),
 *     "limit" = @ContextDefinition("integer",
 *       label = @Translation("Limit"),
 *       description = @Translation("Limit the maximum number of fetched entities. Leave blank for all matching entities."),
 *       default_value = NULL,
 *       required = FALSE
 *     ),
 *   },
 *   provides = {
 *     "entity_fetched" = @ContextDefinition("entity",
 *       label = @Translation("Fetched entity"),
 *       multiple = TRUE
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_entity_fetch_by_field",
  label: new TranslatableMarkup("Fetch entities by field"),
  category: new TranslatableMarkup("Entity"),
  context_definitions: [
    "type" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Entity type"),
      description: new TranslatableMarkup("Specify the type of the entity that should be fetched."),
      options_provider: EntityTypeOptions::class
    ),
    "field_name" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Field name"),
      description: new TranslatableMarkup("Name of the field by which the entity is to be selected."),
      options_provider: FieldListOptions::class
    ),
    "field_value" => new ContextDefinition(
      data_type: "any",
      label: new TranslatableMarkup("Field value"),
      description: new TranslatableMarkup("The field value of the entity to be fetched.")
    ),
    "limit" => new ContextDefinition(
      data_type: "integer",
      label: new TranslatableMarkup("Limit"),
      description: new TranslatableMarkup("Limit the maximum number of fetched entities. Leave blank for all matching entities."),
      default_value: NULL,
      required: FALSE
    ),
  ],
  provides: [
    "entity_fetched" => new ContextDefinition(
      data_type: "entity",
      label: new TranslatableMarkup("Fetched entity"),
      multiple: TRUE
    ),
  ]
)]
class EntityFetchByField extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a EntityFetchByField object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
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
   * {@inheritdoc}
   */
  public function refineContextDefinitions(array $selected_data) {
    if ($type = $this->getContextValue('type')) {
      $this->pluginDefinition['provides']['entity_fetched']->setDataType("entity:$type");
    }
  }

  /**
   * Executes the action with the given context.
   *
   * @param string $type
   *   The entity type id.
   * @param string $field_name
   *   Name of the field by which the entity is to be selected.
   * @param mixed $field_value
   *   The field value of the entity to be fetched.
   * @param int $limit
   *   Limit on the maximum number of fetched entities.
   */
  protected function doExecute($type, $field_name, $field_value, $limit = NULL) {
    $storage = $this->entityTypeManager->getStorage($type);

    // When retrieving entities, if $limit is not set there is no need to use
    // the query object directly.
    if (is_null($limit)) {
      $entities = $storage->loadByProperties([$field_name => $field_value]);
    }
    else {
      $query = $storage->getQuery();
      $entity_ids = $query
        ->accessCheck(TRUE)
        ->condition($field_name, $field_value, '=')
        ->range(0, $limit)
        ->execute();
      $entities = $storage->loadMultiple($entity_ids);
    }

    // Set provided value.
    // @todo Ensure that the provided context has the correct entity type.
    $this->setProvidedValue('entity_fetched', $entities);
  }

}
