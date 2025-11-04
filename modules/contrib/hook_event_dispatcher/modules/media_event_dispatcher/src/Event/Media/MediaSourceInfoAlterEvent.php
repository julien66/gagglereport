<?php

namespace Drupal\media_event_dispatcher\Event\Media;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;
use Drupal\media_event_dispatcher\MediaHookEvents;

/**
 * Class MediaSourceInfoAlterEvent.
 */
#[HookEvent(id: 'media_source_info_alter', alter: 'media_source_info')]
class MediaSourceInfoAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * Get the media source plugin definitions.
   *
   * @return array
   *   The array of media source plugin definitions, keyed by plugin ID.
   */
  public function &getSources(): array {
    return $this->definitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return MediaHookEvents::MEDIA_SOURCE_INFO_ALTER;
  }

}
