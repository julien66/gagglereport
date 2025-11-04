<?php

namespace Drupal\rules\Plugin\Condition;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\Condition;
use Drupal\rules\Core\RulesConditionBase;
use Drupal\rules\TypedData\Options\LanguageOptions;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Path has alias' condition.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @Condition(
 *   id = "rules_path_has_alias",
 *   label = @Translation("Path has alias"),
 *   category = @Translation("Path"),
 *   provider = "path_alias",
 *   context_definitions = {
 *     "path" = @ContextDefinition("string",
 *       label = @Translation("Path"),
 *       description = @Translation("Specifies the existing path you wish to check. For example, '/node/28' or '/forum/1'.")
 *     ),
 *     "language" = @ContextDefinition("language",
 *       label = @Translation("Language"),
 *       description = @Translation("If specified, the language for which the URL alias applies."),
 *       options_provider = "\Drupal\rules\TypedData\Options\LanguageOptions",
 *       default_value = NULL,
 *       required = FALSE
 *     ),
 *   }
 * )
 */
#[Condition(
  id: "rules_path_has_alias",
  label: new TranslatableMarkup("Path has alias"),
  category: new TranslatableMarkup("Path"),
  context_definitions: [
    "path" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Path"),
      description: new TranslatableMarkup("Specifies the existing path you wish to check. For example, '/node/28' or '/forum/1'.")
    ),
    "language" => new ContextDefinition(
      data_type: "language",
      label: new TranslatableMarkup("Language"),
      description: new TranslatableMarkup("If specified, the language for which the URL alias applies."),
      options_provider: LanguageOptions::class,
      default_value: NULL,
      required: FALSE
    ),
  ]
)]
class PathHasAlias extends RulesConditionBase implements ContainerFactoryPluginInterface {

  /**
   * The alias manager service.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * Constructs a PathHasAlias object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The alias manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, AliasManagerInterface $alias_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->aliasManager = $alias_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('path_alias.manager')
    );
  }

  /**
   * Check if a URL path has a URL alias.
   *
   * @param string $path
   *   The path to check.
   * @param \Drupal\Core\Language\LanguageInterface|null $language
   *   An optional language to look up the path in.
   *
   * @return bool
   *   TRUE if the path has an alias in the given language.
   */
  protected function doEvaluate($path, LanguageInterface $language = NULL) {
    $langcode = is_null($language) ? NULL : $language->getId();
    $alias = $this->aliasManager->getAliasByPath($path, $langcode);
    // getAliasByPath() returns the path if there is no alias.
    return $alias != $path;
  }

}
