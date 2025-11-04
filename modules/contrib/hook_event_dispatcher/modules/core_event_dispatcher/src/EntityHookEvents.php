<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for entity hooks.
 */
final class EntityHookEvents {

  /**
   * Control entity operation access.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityAccessEvent
   * @see core_event_dispatcher_entity_access()
   * @see hook_entity_access()
   *
   * @var string
   */
  public const ENTITY_ACCESS = HookEventDispatcherInterface::PREFIX . 'entity.access';

  /**
   * Control entity create access.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityCreateAccessEvent
   * @see core_event_dispatcher_entity_create_access()
   * @see hook_entity_create_access()
   *
   * @var string
   */
  public const ENTITY_CREATE_ACCESS = HookEventDispatcherInterface::PREFIX . 'entity.create_access';

  /**
   * Add to entity type definitions..
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityTypeBuildEvent
   * @see core_event_dispatcher_entity_type_build()
   * @see hook_entity_type_build()
   *
   * @var string
   */
  public const ENTITY_TYPE_BUILD = HookEventDispatcherInterface::PREFIX . 'entity_type.build';

  /**
   * Alter the entity type definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityTypeAlterEvent
   * @see core_event_dispatcher_entity_type_alter()
   * @see hook_entity_type_alter()
   *
   * @var string
   */
  public const ENTITY_TYPE_ALTER = HookEventDispatcherInterface::PREFIX . 'entity_type.alter';

  /**
   * Describe the bundles for entity types.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoEvent
   * @see hook_entity_bundle_info()
   *
   * @var string
   */
  public const ENTITY_BUNDLE_INFO = HookEventDispatcherInterface::PREFIX . 'entity_bundle_info';

  /**
   * Alter the bundles for entity types.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBundleInfoAlterEvent
   * @see hook_entity_bundle_info_alter()
   *
   * @var string
   */
  public const ENTITY_BUNDLE_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'entity_bundle_info.alter';

  /**
   * Act on entity_bundle_create().
   *
   * This hook is invoked after the operation has been performed.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBundleCreateEvent
   * @see hook_entity_bundle_create()
   *
   * @var string
   */
  public const ENTITY_BUNDLE_CREATE = HookEventDispatcherInterface::PREFIX . 'entity_bundle.create';

  /**
   * Act on entity_bundle_delete().
   *
   * This hook is invoked after the operation has been performed.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBundleDeleteEvent
   * @see hook_entity_bundle_delete()
   *
   * @var string
   */
  public const ENTITY_BUNDLE_DELETE = HookEventDispatcherInterface::PREFIX . 'entity_bundle.delete';

  /**
   * Acts when creating a new entity.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityCreateEvent
   * @see core_event_dispatcher_entity_create()
   * @see hook_entity_create()
   *
   * @var string
   */
  public const ENTITY_CREATE = HookEventDispatcherInterface::PREFIX . 'entity.create';

  /**
   * Act on entities when loaded.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityLoadEvent
   * @see core_event_dispatcher_entity_load()
   * @see hook_entity_load()
   *
   * @var string
   */
  public const ENTITY_LOAD = HookEventDispatcherInterface::PREFIX . 'entity.load';

  /**
   * Act on an entity before it is created or updated.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityPresaveEvent
   * @see core_event_dispatcher_entity_presave()
   * @see hook_entity_presave()
   *
   * @var string
   */
  public const ENTITY_PRE_SAVE = HookEventDispatcherInterface::PREFIX . 'entity.presave';

  /**
   * Respond to creation of a new entity.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityInsertEvent
   * @see core_event_dispatcher_entity_insert()
   * @see hook_entity_insert()
   *
   * @var string
   */
  public const ENTITY_INSERT = HookEventDispatcherInterface::PREFIX . 'entity.insert';

  /**
   * Respond to updates to an entity.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityUpdateEvent
   * @see core_event_dispatcher_entity_update()
   * @see hook_entity_update()
   *
   * @var string
   */
  public const ENTITY_UPDATE = HookEventDispatcherInterface::PREFIX . 'entity.update';

  /**
   * Respond to creation of a new entity translation.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityTranslationInsertEvent
   * @see core_event_dispatcher_entity_translation_insert()
   * @see hook_entity_translation_insert()
   *
   * @var string
   */
  public const ENTITY_TRANSLATION_INSERT = HookEventDispatcherInterface::PREFIX . 'entity.translation_insert';

  /**
   * Respond to deletion of a new entity translation.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityTranslationDeleteEvent
   * @see core_event_dispatcher_entity_translation_delete()
   * @see hook_entity_translation_delete()
   *
   * @var string
   */
  public const ENTITY_TRANSLATION_DELETE = HookEventDispatcherInterface::PREFIX . 'entity.translation_delete';

  /**
   * Act before entity deletion.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityPredeleteEvent
   * @see core_event_dispatcher_entity_predelete()
   * @see hook_entity_predelete()
   *
   * @var string
   */
  public const ENTITY_PRE_DELETE = HookEventDispatcherInterface::PREFIX . 'entity.predelete';

  /**
   * Respond to entity deletion.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityDeleteEvent
   * @see core_event_dispatcher_entity_delete()
   * @see hook_entity_delete()
   *
   * @var string
   */
  public const ENTITY_DELETE = HookEventDispatcherInterface::PREFIX . 'entity.delete';

  /**
   * Act on entities being assembled before rendering.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityViewEvent
   * @see core_event_dispatcher_entity_view()
   * @see hook_entity_view()
   *
   * @var string
   */
  public const ENTITY_VIEW = HookEventDispatcherInterface::PREFIX . 'entity.view';

  /**
   * Alter a entity being assembled right before rendering.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityViewAlterEvent
   * @see core_event_dispatcher_entity_view_alter()
   * @see hook_entity_view_alter()
   *
   * @var string
   */
  public const ENTITY_VIEW_ALTER = HookEventDispatcherInterface::PREFIX . 'entity.view_alter';

  /**
   * Alter entity renderable values before cache checking in drupal_render().
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBuildDefaultsAlterEvent
   * @see core_event_dispatcher_entity_build_defaults_alter()
   * @see hook_entity_build_defaults_alter()
   *
   * @var string
   */
  public const ENTITY_BUILD_DEFAULTS_ALTER = HookEventDispatcherInterface::PREFIX . 'entity.build_defaults_alter';

  /**
   * Provides custom base field definitions for a content entity type.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoEvent
   * @see core_event_dispatcher_entity_base_field_info()
   * @see hook_entity_base_field_info()
   *
   * @var string
   */
  public const ENTITY_BASE_FIELD_INFO = HookEventDispatcherInterface::PREFIX . 'entity_base.field_info';

  /**
   * Alter base field definitions for a content entity type.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBaseFieldInfoAlterEvent
   * @see core_event_dispatcher_entity_base_field_info_alter()
   * @see hook_entity_base_field_info_alter()
   *
   * @var string
   */
  public const ENTITY_BASE_FIELD_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'entity_base.field_info_alter';

  /**
   * Alter bundle field definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityBundleFieldInfoAlterEvent
   * @see core_event_dispatcher_entity_bundle_field_info_alter()
   * @see hook_entity_bundle_field_info_alter()
   *
   * @var string
   */
  public const ENTITY_BUNDLE_FIELD_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'entity_bundle.field_info_alter';

  /**
   * Entity operation.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityOperationEvent
   * @see core_event_dispatcher_entity_operation()
   * @see hook_entity_operation()
   *
   * @var string
   */
  public const ENTITY_OPERATION = HookEventDispatcherInterface::PREFIX . 'entity.operation';

  /**
   * Entity operation alter.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityOperationAlterEvent
   * @see core_event_dispatcher_entity_operation_alter()
   * @see hook_entity_operation_alter()
   *
   * @var string
   */
  public const ENTITY_OPERATION_ALTER = HookEventDispatcherInterface::PREFIX . 'entity.operation_alter';

  /**
   * Control access to fields.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityFieldAccessEvent
   * @see core_event_dispatcher_entity_field_access()
   * @see hook_entity_field_access()
   *
   * @var string
   */
  public const ENTITY_FIELD_ACCESS = HookEventDispatcherInterface::PREFIX . 'entity_field.access';

  /**
   * Exposes "pseudo-field" components on content entities.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoEvent
   * @see core_event_dispatcher_entity_extra_field_info()
   * @see hook_entity_extra_field_info()
   *
   * @var string
   */
  public const ENTITY_EXTRA_FIELD_INFO = HookEventDispatcherInterface::PREFIX . 'entity_extra_field.info';

  /**
   * Alter "pseudo-field" components on content entities.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoAlterEvent
   * @see core_event_dispatcher_entity_extra_field_info_alter()
   * @see hook_entity_extra_field_info_alter()
   *
   * @var string
   */
  public const ENTITY_EXTRA_FIELD_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'entity_extra_field.info_alter';

}
