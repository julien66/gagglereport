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
 * Provides a 'Delete alias for a path' action.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @RulesAction(
 *   id = "rules_path_alias_delete_by_path",
 *   label = @Translation("Delete all aliases for a path"),
 *   category = @Translation("Path"),
 *   provider = "path_alias",
 *   context_definitions = {
 *     "path" = @ContextDefinition("string",
 *       label = @Translation("Existing system path"),
 *       description = @Translation("Specifies the existing path for which you wish to delete the alias. For example, '/node/1'. Use an absolute path and do not add a trailing slash.")
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_path_alias_delete_by_path",
  label: new TranslatableMarkup("Delete all aliases for a path"),
  category: new TranslatableMarkup("Path"),
  provider: "path_alias",
  context_definitions: [
    "path" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Existing system path"),
      description: new TranslatableMarkup("Specifies the existing path for which you wish to delete the alias. For example, '/node/1'. Use an absolute path and do not add a trailing slash.")
    ),
  ]
)]
class PathAliasDeleteByPath extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The alias storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $aliasStorage;

  /**
   * Constructs a PathAliasDeleteByPath object.
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
   * Delete existing aliases for a given path.
   *
   * @param string $path
   *   Existing system path.
   */
  protected function doExecute($path) {
    $aliases = $this->aliasStorage->loadByProperties(['path' => $path]);
    $this->aliasStorage->delete($aliases);
  }

}
