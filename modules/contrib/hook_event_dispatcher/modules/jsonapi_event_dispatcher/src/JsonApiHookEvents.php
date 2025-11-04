<?php

namespace Drupal\jsonapi_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for jsonapi hooks.
 */
final class JsonApiHookEvents {

  /**
   * Controls access when filtering by entity data via JSON:API.
   *
   * @Event
   *
   * @see \Drupal\jsonapi_event_dispatcher\Event\JsonApiEntityFilterAccessEvent
   * @see jsonapi_event_dispatcher_jsonapi_entity_filter_access()
   * @see hook_jsonapi_entity_filter_access()
   *
   * @var string
   */
  public const JSONAPI_ENTITY_FILTER_ACCESS = HookEventDispatcherInterface::PREFIX . 'jsonapi.entity_filter_access';

  /**
   * Restricts filtering access to the given field.
   *
   * @Event
   *
   * @see \Drupal\jsonapi_event_dispatcher\Event\JsonApiEntityFieldFilterAccessEvent
   * @see jsonapi_event_dispatcher_jsonapi_entity_field_filter_access()
   * @see hook_jsonapi_entity_field_filter_access()
   *
   * @var string
   */
  public const JSONAPI_ENTITY_FIELD_FILTER_ACCESS = HookEventDispatcherInterface::PREFIX . 'jsonapi.entity_field_filter_access';

}
