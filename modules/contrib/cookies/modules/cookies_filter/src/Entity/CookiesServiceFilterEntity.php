<?php

namespace Drupal\cookies_filter\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Cookie service filter entity.
 *
 * @ConfigEntityType(
 *   id = "cookies_service_filter",
 *   label = @Translation("COOKiES Service Filter"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cookies_filter\CookiesServiceFilterEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\cookies_filter\Form\CookiesServiceFilterEntityForm",
 *       "edit" = "Drupal\cookies_filter\Form\CookiesServiceFilterEntityForm",
 *       "delete" = "Drupal\cookies_filter\Form\CookiesServiceFilterEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\cookies\CookiesRouteProvider",
 *     },
 *   },
 *   config_prefix = "cookies_service_filter",
 *   admin_permission = "access cookies_service_filter",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "langcode",
 *     "id",
 *     "label",
 *     "status",
 *     "dependencies",
 *     "service",
 *     "elementType",
 *     "elementSelectors",
 *     "placeholderBehaviour",
 *     "placeholderCustomElementSelectors",
 *   },
 *   links = {
 *     "canonical" = "/admin/config/system/cookies/cookies-service-filter/{cookies_service_filter}",
 *     "add-form" = "/admin/config/system/cookies/cookies-service-filter/add",
 *     "edit-form" = "/admin/config/system/cookies/cookies-service-filter/{cookies_service_filter}/edit",
 *     "delete-form" = "/admin/config/system/cookies/cookies-service-filter/{cookies_service_filter}/delete",
 *     "collection" = "/admin/config/system/cookies/cookies-service-filter"
 *   }
 * )
 */
class CookiesServiceFilterEntity extends ConfigEntityBase {

  /**
   * The Cookie service filter entity ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Cookie service filter entity label.
   *
   * @var string
   */
  protected $label;


  /**
   * The Cookie service the service filter belongs to.
   *
   * @var string
   */
  protected $service;

  /**
   * The Cookie service filter element type (iframe, object, embed, script, ...)
   *
   * @var string
   */
  protected $elementType;

  /**
   * The Cookie service filter element selectors.
   *
   * @var string
   */
  protected $elementSelectors;

  /**
   * The Cookie service filter element placeholder behaviour selection.
   *
   * Can be:
   * - 'overlay' = Cookies Overlay Placeholder
   * - 'hide' = hide blocked element
   * - 'none' = keep as-is
   * to switch the placeholder behaviour.
   *
   * @var string
   */
  protected $placeholderBehaviour;

  /**
   * The Cookie service filter element placeholder selectors.
   *
   * If null, use the $elementSelectors wrapper element (default).
   *
   * @var string
   */
  protected $placeholderCustomElementSelectors;

}
