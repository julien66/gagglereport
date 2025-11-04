<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Options provider to return all fields in the system.
 */
class FieldListOptions extends OptionsProviderBase implements ContainerInjectionInterface {

  /**
   * The entity field manager service.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs a FieldListOptions object.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager service.
   */
  public function __construct(EntityFieldManagerInterface $entity_field_manager) {
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_field.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPossibleOptions(AccountInterface $account = NULL) {
    $options = [];

    // Load all the fields in the system.
    $fields = $this->entityFieldManager->getFieldMap();

    // Add each field to our options array.
    foreach ($fields as $entity_fields) {
      foreach ($entity_fields as $field_name => $field) {
        $options[$field_name] = $field_name . ' (' . $field['type'] . ')';
      }
    }

    // Sort the result by value for ease of locating and selecting.
    asort($options);

    return $options;
  }

}
