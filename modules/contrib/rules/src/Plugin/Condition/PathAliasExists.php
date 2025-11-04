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
 * Provides a 'Path alias exists' condition.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @Condition(
 *   id = "rules_path_alias_exists",
 *   label = @Translation("Path alias exists"),
 *   category = @Translation("Path"),
 *   provider = "path_alias",
 *   context_definitions = {
 *     "alias" = @ContextDefinition("string",
 *       label = @Translation("Path alias"),
 *       description = @Translation("Specify the path alias to check for. For example, '/about' for an about page.")
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
  id: "rules_path_alias_exists",
  label: new TranslatableMarkup("Path alias exists"),
  category: new TranslatableMarkup("Path"),
  context_definitions: [
    "alias" => new ContextDefinition(
      data_type: "string",
      label: new TranslatableMarkup("Path alias"),
      description: new TranslatableMarkup("Specify the path alias to check for. For example, '/about' for an about page.")
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
class PathAliasExists extends RulesConditionBase implements ContainerFactoryPluginInterface {

  /**
   * The alias manager service.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * Constructs a PathAliasExists object.
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
   * Check if a path alias exists.
   *
   * @param string $alias
   *   The alias to see if exists.
   * @param \Drupal\Core\Language\LanguageInterface|null $language
   *   The language to use.
   *
   * @return bool
   *   TRUE if the system path does not match the given alias (ie: the alias
   *   exists).
   */
  protected function doEvaluate($alias, LanguageInterface $language = NULL) {
    $langcode = is_null($language) ? NULL : $language->getId();
    $path = $this->aliasManager->getPathByAlias($alias, $langcode);
    // getPathByAlias() returns the alias if there is no path.
    return $path != $alias;
  }

}
