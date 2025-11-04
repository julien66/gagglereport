<?php

declare(strict_types=1);

namespace Drupal\rules\Core\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a RulesAction attribute object.
 *
 * Plugin Namespace: Plugin\RulesAction.
 *
 * For a working example, see \Drupal\rules\Plugin\RulesAction\BanIP
 *
 * @see \Drupal\rules\Core\RulesActionInterface
 * @see \Drupal\rules\Core\RulesActionManagerInterface
 * @see \Drupal\rules\Core\RulesActionBase
 * @see plugin_api
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class RulesAction extends Plugin {

  /**
   * An array of context definitions describing the context used by the plugin.
   *
   * Array keys are the names of the context variables and values are the
   * context definitions.
   *
   * @var \Drupal\rules\Context\ContextDefinition[]|null
   */
  public readonly ?array $context_definitions;

  /**
   * The permission required to access the configuration UI for this plugin.
   *
   * @var string[]|null
   *   Array of permission strings as declared in a *.permissions.yml file. If
   *   any one of these permissions apply for the relevant user, we allow
   *   access.
   */
  public readonly ?array $configure_permissions;

  /**
   * Defines the provided context_definitions of the action plugin.
   *
   * Array keys are the names of the context variables and values are the
   * context definitions.
   *
   * @var \Drupal\rules\Context\ContextDefinition[]|null
   */
  public readonly ?array $provides;

  /**
   * Constructs a RulesAction attribute.
   *
   * @param string $id
   *   The plugin ID.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $label
   *   The human-readable name of the action.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $category
   *   The category under which the action should be listed in the UI.
   * @param string|null $provider
   *   The provider of the attribute class.
   * @param \Drupal\Core\Annotation\ContextDefinition[] $context_definitions
   *   (optional) An array of context definitions describing the context used by
   *   the plugin.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   * @param string[]|null $configure_permissions
   *   (optional) The permission required to access the configuration UI for
   *   this plugin.
   * @param \Drupal\Core\Annotation\ContextDefinition[]|null $provides
   *   Defines the provided context_definitions of the action plugin.
   */
  public function __construct(
    public readonly string $id,
    public readonly ?TranslatableMarkup $label = NULL,
    public readonly ?TranslatableMarkup $category = NULL,
    public ?string $provider = NULL,
    array|null $context_definitions = [],
    public readonly ?string $deriver = NULL,
    array|null $configure_permissions = [],
    array|null $provides = [],
  ) {
    $this->context_definitions = $context_definitions;
    $this->provider = $provider;
    $this->configure_permissions = $configure_permissions;
    $this->provides = $provides;
  }

}
