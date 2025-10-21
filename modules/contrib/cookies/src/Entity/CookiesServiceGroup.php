<?php

namespace Drupal\cookies\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Cookie service group entity.
 *
 * @ConfigEntityType(
 *   id = "cookies_service_group",
 *   label = @Translation("COOKiES service group"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cookies\CookiesServiceGroupListBuilder",
 *     "form" = {
 *       "add" = "Drupal\cookies\Form\CookiesServiceGroupForm",
 *       "edit" = "Drupal\cookies\Form\CookiesServiceGroupForm",
 *       "delete" = "Drupal\cookies\Form\CookiesServiceGroupDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\cookies\CookiesRouteProvider",
 *     },
 *   },
 *   config_prefix = "cookies_service_group",
 *   admin_permission = "administer cookies services and service groups",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "title" = "title",
 *     "details" = "details",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "langcode",
 *     "id",
 *     "label",
 *     "status",
 *     "dependencies",
 *     "weight",
 *     "title",
 *     "details"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/system/cookies/cookies-service-group/{cookies_service_group}",
 *     "add-form" = "/admin/config/system/cookies/cookies-service-group/add",
 *     "edit-form" = "/admin/config/system/cookies/cookies-service-group/{cookies_service_group}/edit",
 *     "delete-form" = "/admin/config/system/cookies/cookies-service-group/{cookies_service_group}/delete",
 *     "collection" = "/admin/config/system/cookies/cookies-service-group"
 *   }
 * )
 */
class CookiesServiceGroup extends ConfigEntityBase implements CookiesServiceGroupInterface {

  /**
   * The Cookie service group ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Cookie service group label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Cookie service group weight.
   *
   * @var int
   */
  protected $weight = 0;

  /**
   * The Cookie service group title.
   *
   * @var string
   */
  protected $title;

  /**
   * The Cookie service group details or description.
   *
   * @var string
   */
  protected $details;

  /**
   * The theme definition.
   */
  public const THEME_DEFINITION = 'cookies_docs_group';

  /**
   * Get the Cookie service group label.
   */
  public function getWeight() {
    return $this->weight;
  }

  /**
   * Get the Cookie service group title.
   */
  public function getTitle() {
    return $this->title;
  }

  /**
   * Get the Cookie service group details or description.
   */
  public function getDetails() {
    return $this->details;
  }

  /**
   * Set the Cookie service group ID.
   *
   * @param string $id
   *   The Cookie service group ID.
   *
   * @return self
   *   The cookies service group entity.
   */
  public function setId(string $id) {
    $this->id = $id;

    return $this;
  }

  /**
   * Set the Cookie service group weight.
   *
   * @param int $weight
   *   The Cookie service group weight.
   *
   * @return self
   *   The cookies service group entity.
   */
  public function setWeight(int $weight) {
    $this->weight = $weight;

    return $this;
  }

  /**
   * Set the Cookie service group title.
   *
   * @param string $title
   *   The Cookie service group title.
   *
   * @return self
   *   The cookies service group entity.
   */
  public function setTitle(string $title) {
    $this->title = $title;

    return $this;
  }

  /**
   * Set the Cookie service group details or description.
   *
   * @param string $details
   *   The Cookie service group details or description.
   *
   * @return self
   *   The cookies service group entity.
   */
  public function setDetails(string $details) {
    $this->details = $details;

    return $this;
  }

  /**
   * Returns the render array representation of the service group.
   */
  public function toRenderArray(): array {
    $renderArray = [
      '#theme' => static::THEME_DEFINITION,
      '#label' => $this->label(),
      '#weight' => $this->getWeight(),
      '#title' => $this->getTitle(),
      '#details' => $this->getDetails(),
      '#contextual_links' => [
        'block' => [
          'route_parameters' => ['block' => $this->id()],
        ],
      ],
    ];
    $servicesRenderArray = [];
    $services = $this->getServices();
    if (!empty($services)) {
      foreach ($services as $service) {
        $servicesRenderArray[$service->id()] = $service->toRenderArray();
      }
    }
    $renderArray['#services'] = $servicesRenderArray;
    return $renderArray;
  }

  /**
   * Returns the group's services.
   *
   * Warning: This is not a simple getter, but loads from EntityTypeManager.
   *
   * @param array $properties
   *   Additional property filters for the services.
   *
   * @return \Drupal\cookies\Entity\CookiesServiceEntity[]
   *   The group's CookiesServiceEntities.
   */
  public function getServices(array $properties = ['status' => 1]) {
    $properties = array_merge($properties, ['group' => $this->id()]);
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $serviceEntities = $entityTypeManager
      ->getStorage('cookies_service')
      ->loadByProperties($properties);
    return $serviceEntities;
  }

}
