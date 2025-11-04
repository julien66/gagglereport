<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;
use Drupal\rules\TypedData\Options\LanguageOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Create any path alias' action.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @RulesAction(
 *   id = "rules_path_alias_create",
 *   label = @Translation("Create any path alias"),
 *   category = @Translation("Path"),
 *   provider = "path_alias",
 *   context_definitions = {
 *     "source" = @ContextDefinition("string",
 *       label = @Translation("Existing system path"),
 *       description = @Translation("Specifies the existing path you wish to alias. For example, '/node/28' or '/forum/1'.")
 *     ),
 *     "alias" = @ContextDefinition("string",
 *       label = @Translation("Path alias"),
 *       description = @Translation("Specify an alternative path by which this data can be accessed. For example, '/about' for an about page. Use an absolute path and do not add a trailing slash.")
 *     ),
 *     "language" = @ContextDefinition("language",
 *       label = @Translation("Language"),
 *       description = @Translation("If specified, the language for which the path alias applies."),
 *       options_provider = "\Drupal\rules\TypedData\Options\LanguageOptions",
 *       default_value = NULL,
 *       required = FALSE
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_path_alias_create",
  label: new TranslatableMarkup("Create any path alias"),
  category: new TranslatableMarkup("Path"),
  provider: "path_alias",
  context_definitions: [
    "source" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Existing system path"),
      description: new TranslatableMarkup("Specifies the existing path you wish to alias. For example, '/node/28' or '/forum/1'.")
    ),
    "alias" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Path alias"),
      description: new TranslatableMarkup("Specify an alternative path by which this data can be accessed. For example, '/about' for an about page. Use an absolute path and do not add a trailing slash.")
    ),
    "language" => new ContextDefinition(
      data_type: "language",
      label: new TranslatableMarkup("Language"),
      description: new TranslatableMarkup("If specified, the language for which the path alias applies."),
      options_provider: LanguageOptions::class,
      default_value: NULL,
      required: FALSE
    ),
  ]
)]
class PathAliasCreate extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * The alias storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $aliasStorage;

  /**
   * Constructs a PathAliasCreate object.
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
   * Creates an alias for an existing path.
   *
   * @param string $source
   *   The existing path that should be aliased.
   * @param string $alias
   *   The alias path that should be created.
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   (optional) The language.
   */
  protected function doExecute($source, $alias, LanguageInterface $language = NULL) {
    $langcode = isset($language) ? $language->getId() : LanguageInterface::LANGCODE_NOT_SPECIFIED;
    $path_alias = $this->aliasStorage->create([
      'path' => $source,
      'alias' => $alias,
      'langcode' => $langcode,
    ]);
    $path_alias->save();
  }

}
