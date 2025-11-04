<?php

namespace Drupal\rules\Plugin\RulesAction;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Core\Attribute\RulesAction;
use Drupal\rules\Core\RulesActionBase;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides "Block User" action.
 *
 * @todo Add access callback information from Drupal 7.
 *
 * @RulesAction(
 *   id = "rules_user_block",
 *   label = @Translation("Block a user"),
 *   category = @Translation("User"),
 *   context_definitions = {
 *     "user" = @ContextDefinition("entity:user",
 *       label = @Translation("User"),
 *       description = @Translation("Specifies the user that should be blocked.")
 *     ),
 *   }
 * )
 */
#[RulesAction(
  id: "rules_user_block",
  label: new TranslatableMarkup("Block a user"),
  category: new TranslatableMarkup("User"),
  context_definitions: [
    "user" => new ContextDefinition(
      data_type: "entity:user",
      label: new TranslatableMarkup("User"),
      description: new TranslatableMarkup("Specifies the user that should be blocked.")
    ),
  ]
)]
class UserBlock extends RulesActionBase implements ContainerFactoryPluginInterface {

  /**
   * Session manager.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  protected $sessionManager;

  /**
   * Constructs a UserBlock object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   *   The session manager service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, SessionManagerInterface $session_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->sessionManager = $session_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('session_manager')
    );
  }

  /**
   * Flag that indicates if the entity should be auto-saved later.
   *
   * @var bool
   */
  protected $saveLater = FALSE;

  /**
   * Block a user.
   *
   * @param \Drupal\user\UserInterface $user
   *   The user object.
   */
  protected function doExecute(UserInterface $user) {
    // Do nothing if user is anonymous or already blocked.
    if ($user->isAuthenticated() && $user->isActive()) {
      $user->block();
      $this->sessionManager->delete($user->id());
      // Set flag that indicates if the entity should be auto-saved later.
      $this->saveLater = TRUE;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function autoSaveContext() {
    if ($this->saveLater) {
      return ['user'];
    }
    return [];
  }

}
