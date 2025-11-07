<?php

namespace Drupal\action_example\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ActionBase;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A basic example action that does nothing.
 *
 * @Action(
 *   id = "action_example_basic_action",
 *   label = @Translation("Action Example: A basic example action that does nothing"),
 *   type = "system"
 * )
 */
class BasicExample extends ActionBase implements ContainerFactoryPluginInterface {

  /**
   * Construct a BasicExample object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MessengerInterface $messenger) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->setMessenger($messenger);
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function execute($object = NULL) {
    $this->messenger()->addMessage($this->t('action_example_basic_action fired'));
  }

  /**
   * {@inheritDoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    $result = AccessResult::allowed();
    return $return_as_object ? $result : $result->isAllowed();
  }

}
