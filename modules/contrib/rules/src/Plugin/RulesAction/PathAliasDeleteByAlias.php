<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Delete any path alias' action.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @RulesAction(
 *   id = "rules_path_alias_delete_by_alias",
 *   label = @Translation("Delete path alias"),
 *   category = @Translation("Path"),
 *   provider = "path_alias",
 *   context_definitions = {
 *     "alias" = @ContextDefinition("string",
 *       label = @Translation("Existing system path alias"),
 *       description = @Translation("Specifies the existing path alias you wish to delete. For example, '/about/team'. Use an absolute path and do not add a trailing slash.")
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_path_alias_delete_by_alias",
  label: new TranslatableMarkup("Delete path alias"),
  category: new TranslatableMarkup("Path"),
  provider: "path_alias",
  context_definitions: [
    "alias" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Existing system path alias"),
      description: new TranslatableMarkup("Specifies the existing path alias you wish to delete. For example, '/about/team'. Use an absolute path and do not add a trailing slash.")
    ),
  ]
)]
class PathAliasDeleteByAlias extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The alias storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $aliasStorage;

  /**
   * Constructs a PathAliasDeleteByAlias object.
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
   * Delete an existing alias.
   *
   * @param string $alias
   *   Alias to be deleted.
   */
  protected function doExecute($alias) {
    $aliases = $this->aliasStorage->loadByProperties(['alias' => $alias]);
    $this->aliasStorage->delete($aliases);
  }

}
