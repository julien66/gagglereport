<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Options provider to list all entity types.
 */
class EntityTypeOptions extends OptionsProviderBase implements ContainerInjectionInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a EntityTypeOptions object.
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

    // Load all the entity types.
    $entity_types = $this->entityTypeManager->getDefinitions();
    foreach ($entity_types as $entity_type) {
      if (!$entity_type instanceof ContentEntityTypeInterface) {
        continue;
      }
      $options[$entity_type->id()] = (string) $entity_type->getLabel();
      // If the id differs from the label add the id in brackets for clarity.
      if (strtolower(str_replace('_', ' ', $entity_type->id())) != strtolower($entity_type->getLabel())) {
        $options[$entity_type->id()] .= ' (' . $entity_type->id() . ')';
      }
    }

    // Sort the result by value for ease of locating and selecting.
    asort($options);

    return $options;
  }

}
