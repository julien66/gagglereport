<?php

namespace Drupal\media_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for media hooks.
 */
final class MediaHookEvents {

  /**
   * Alters the information provided in \Drupal\media\Annotation\MediaSource.
   *
   * @Event
   *
   * @see \Drupal\media_event_dispatcher\Event\Media\MediaSourceInfoAlterEvent
   * @see media_event_dispatcher_media_source_info_alter()
   * @see hook_media_source_info_alter()
   *
   * @var string
   */
  public const MEDIA_SOURCE_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'media.source_info_alter';

  /**
   * Alters an oEmbed resource URL before it is fetched.
   *
   * @Event
   *
   * @see \Drupal\media_event_dispatcher\Event\Media\OEmbedResourceUrlAlterEvent
   * @see media_event_dispatcher_oembed_resource_url_alter()
   * @see hook_oembed_resource_url_alter()
   *
   * @var string
   */
  public const MEDIA_OEMBED_RESOURCE_DATA_ALTER = HookEventDispatcherInterface::PREFIX . 'media.oembed_url_alter';

}
