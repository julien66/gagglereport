<?php

namespace Drupal\rules\TypedData\Options;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Options provider for entity bundles.
 *
 * The returned top-level array is keyed on the bundle label, with nested arrays
 * keyed on the bundle machine name.
 */
class EntityBundleOptions extends OptionsProviderBase implements ContainerInjectionInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type bundle information manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityBundleInfo;

  /**
   * Constructs a EntityBundleOptions object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_bundle_info
   *   The entity type bundle information manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityTypeBundleInfoInterface $entity_bundle_info) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityBundleInfo = $entity_bundle_info;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_type.bundle.info')
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

      // Get the bundles for this entity type.
      $bundles = $this->entityBundleInfo->getBundleInfo($entity_type->id());

      // Transform the $bundles array into a form suitable for select options.
      array_walk($bundles, function (&$value, $key) {
        // Flatten to just the label text.
        $value = (string) $value['label'];
        // If the key differs from the label add the key in brackets.
        if (strtolower(str_replace('_', ' ', $key)) != strtolower($value)) {
          $value .= ' (' . $key . ')';
        }
      });
      $options[(string) $entity_type->getLabel()] = $bundles;

    }

    // Sort the result by key, which is the group name.
    ksort($options);

    return $options;
  }

}
