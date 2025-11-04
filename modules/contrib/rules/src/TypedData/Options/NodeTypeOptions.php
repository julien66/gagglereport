<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Options provider to list all node types.
 */
class NodeTypeOptions extends OptionsProviderBase implements ContainerInjectionInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a NodeTypeOptions object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $options = [];

    // Load all the node types.
    $node_types = $this->entityTypeManager->getStorage('node_type')->loadMultiple();

    foreach ($node_types as $node_type) {
      $options[$node_type->id()] = $node_type->label();
      // If the id differs from the label add the id in brackets for clarity.
      if (strtolower(str_replace('_', ' ', $node_type->id())) != strtolower($node_type->label())) {
        $options[$node_type->id()] .= ' (' . $node_type->id() . ')';
      }
    }

    // Sort the result by value for ease of locating and selecting.
    asort($options);

    return $options;
  }

}
