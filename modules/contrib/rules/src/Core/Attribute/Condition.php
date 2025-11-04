<?php

declare(strict_types=1);

namespace Drupal\rules\Core\Attribute;

use Drupal\Core\Condition\Attribute\Condition as CoreConditionAttribute;

use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Extension of the core Condition attribute class.
 *
 * This class adds a "configuration_access" parameter and a "provides" parameter
 * to the Condition attribute.
 *
 * The keys should be used as follows:
 *
 * #[Condition(
 *   id: "my_module_user_is_blocked",
 *   label: new TranslatableMarkup("My User is blocked"),
 *   category: new TranslatableMarkup("User"),
 *   context_definitions: [
 *     "user" => new ContextDefinition(
 *       data_type: "entity:user",
 *       label: new TranslatableMarkup("User")
 *      ),
 *   ],
 *   provides: [
 *     "status" => new ContextDefinition(
 *       data_type: "string",
 *       label: new TranslatableMarkup("Reason my user is blocked")
 *     ),
 *   ],
 *   configure_permissions: [
 *     "administer users",
 *     "block users",
 *   ],
 * )
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Condition extends CoreConditionAttribute {

  /**
   * The permission required to access the configuration UI for this plugin.
   *
   * @var string[]|null
   *   Array of permission strings as declared in a *.permissions.yml file. If
   *   any one of these permissions apply for the relevant user, we allow
   *   access.
   *
   * @phpcs:disable Drupal.NamingConventions.ValidVariableName.LowerCamelName
   */
  public readonly ?array $configure_permissions;
  // phpcs:enable

  /**
   * Defines the provided context_definitions of the condition plugin.
   *
   * Array keys are the names of the context variables and values are the
   * context definitions.
   *
   * @var \Drupal\rules\Context\ContextDefinition[]|null
   */
  public readonly ?array $provides;

  /**
   * Constructs a Condition attribute.
   *
   * @param string $id
   *   The plugin ID.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $label
   *   (optional) The human-readable name of the condition.
   * @param string|null $module
   *   (optional) The name of the module providing the type.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup|null $category
   *   (optional) The category under which the condition should be listed in the
   *   UI.
   * @param \Drupal\rules\Context\ContextDefinition[] $context_definitions
   *   (optional) An array of context definitions describing the context used by
   *   the plugin.
   * @param class-string|null $deriver
   *   (optional) The deriver class.
   * @param string|null $provider
   *   (optional) The provider of the attribute class.
   * @param string[]|null $configure_permissions
   *   (optional) The permission required to access the configuration UI for
   *   this plugin.
   * @param \Drupal\rules\Context\ContextDefinition[]|null $provides
   *   (optional) Defines the provided context_definitions of the action plugin.
   */
  public function __construct(
    string $id,
    ?TranslatableMarkup $label,
    ?string $module = NULL,
    ?TranslatableMarkup $category = NULL,
    array $context_definitions = [],
    ?string $deriver = NULL,
    // Parameter missing from the core Condition attribute:
    public ?string $provider = NULL,
    // Parameters added by Rules:
    array|null $configure_permissions = [],
    array|null $provides = [],
  ) {
    parent::__construct($id, $label, $module, $category, $context_definitions, $deriver);
    $this->provider = $provider;
    $this->configure_permissions = $configure_permissions;
    $this->provides = $provides;
  }

}
