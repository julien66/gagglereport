<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for core hooks.
 */
final class CoreHookEvents {

  /**
   * Perform periodic actions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\CronEvent
   * @see core_event_dispatcher_cron()
   * @see hook_cron()
   *
   * @var string
   */
  public const CRON = HookEventDispatcherInterface::PREFIX . 'cron';

  /**
   * Alter available data types for typed data wrappers.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\DataTypeInfoAlterEvent
   * @see core_event_dispatcher_data_type_info_alter()
   * @see hook_data_type_info_alter()
   *
   * @var string
   */
  public const DATA_TYPE_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'data_type.info_alter';

  /**
   * Alter cron queue information before cron runs.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\QueueInfoAlterEvent
   * @see core_event_dispatcher_queue_info_alter()
   * @see hook_queue_info_alter()
   *
   * @var string
   */
  public const QUEUE_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'queue.info_alter';

  /**
   * Alter an email message created with MailManagerInterface->mail().
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\MailAlterEvent
   * @see core_event_dispatcher_mail_alter()
   * @see hook_mail_alter()
   *
   * @var string
   */
  public const MAIL_ALTER = HookEventDispatcherInterface::PREFIX . 'mail.alter';

  /**
   * Alter the list of mail backend plugin definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\MailBackendInfoAlterEvent
   * @see core_event_dispatcher_mail_backend_info_alter()
   * @see hook_mail_backend_info_alter()
   *
   * @var string
   */
  public const MAIL_BACKEND_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'mail_backend.info_alter';

  /**
   * Alter the default country list.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\CountriesAlterEvent
   * @see core_event_dispatcher_countries_alter()
   * @see hook_countries_alter()
   *
   * @var string
   */
  public const COUNTRIES_ALTER = HookEventDispatcherInterface::PREFIX . 'countries.alter';

  /**
   * Alter display variant plugin definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\DisplayVariantPluginAlterEvent
   * @see core_event_dispatcher_display_variant_plugin_alter()
   * @see hook_display_variant_plugin_alter()
   *
   * @var string
   */
  public const DISPLAY_VARIANT_PLUGIN_ALTER = HookEventDispatcherInterface::PREFIX . 'display_variant.plugin_alter';

  /**
   * Allow modules to alter layout plugin definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\LayoutAlterEvent
   * @see core_event_dispatcher_layout_alter()
   * @see hook_layout_alter()
   *
   * @var string
   */
  public const LAYOUT_ALTER = HookEventDispatcherInterface::PREFIX . 'layout.alter';

  /**
   * Flush all persistent and static caches.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\CacheFlushEvent
   * @see core_event_dispatcher_cache_flush()
   * @see hook_cache_flush()
   *
   * @var string
   */
  public const CACHE_FLUSH = HookEventDispatcherInterface::PREFIX . 'cache_flush';

  /**
   * Rebuild data based upon refreshed caches.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\RebuildEvent
   * @see core_event_dispatcher_rebuild()
   * @see hook_rebuild()
   *
   * @var string
   */
  public const REBUILD = HookEventDispatcherInterface::PREFIX . 'rebuild';

  /**
   * Alter the configuration synchronization steps.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\ConfigImportStepsAlterEvent
   * @see core_event_dispatcher_config_import_steps_alter()
   * @see hook_config_import_steps_alter()
   *
   * @var string
   */
  public const CONFIG_IMPORT_STEPS_ALTER = HookEventDispatcherInterface::PREFIX . 'config.import_steps_alter';

  /**
   * Alter config typed data definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\ConfigSchemaInfoAlterEvent
   * @see core_event_dispatcher_config_schema_info_alter()
   * @see hook_config_schema_info_alter()
   *
   * @var string
   */
  public const CONFIG_SCHEMA_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'config.schema.info_alter';

  /**
   * Alter validation constraint plugin definitions.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Core\ValidationConstraintAlterEvent
   * @see core_event_dispatcher_validation_constraint_alter()
   * @see hook_validation_constraint_alter()
   *
   * @var string
   */
  public const VALIDATION_CONSTRAINT_ALTER = HookEventDispatcherInterface::PREFIX . 'validation_constraint.alter';

}
