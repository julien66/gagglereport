<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a generic 'Create entity path alias' action.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @RulesAction(
 *   id = "rules_entity_path_alias_create",
 *   deriver = "Drupal\rules\Plugin\RulesAction\EntityPathAliasCreateDeriver"
 * )
 */
#[RulesAction(
  id: "rules_entity_path_alias_create",
  deriver: EntityPathAliasCreateDeriver::class
)]
class EntityPathAliasCreate extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The alias storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $aliasStorage;

  /**
   * The entity type id.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * Constructs an EntityPathAliasCreate object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityStorageInterface $alias_storage
   *   The alias storage service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityStorageInterface $alias_storage) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->aliasStorage = $alias_storage;
    $this->entityTypeId = $plugin_definition['entity_type_id'];
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')->getStorage('path_alias')
    );
  }

  /**
   * Creates entity path alias.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity that should get an alias.
   * @param string $alias
   *   The alias to be created.
   */
  protected function doExecute(EntityInterface $entity, $alias) {
    // We need to save the entity before we can get its internal path.
    if ($entity->isNew()) {
      $entity->save();
    }

    $path = '/' . $entity->toUrl()->getInternalPath();
    $langcode = $entity->language()->getId();
    $path_alias = $this->aliasStorage->create([
      'path' => $path,
      'alias' => $alias,
      'langcode' => $langcode,
    ]);
    $path_alias->save();
  }

}
