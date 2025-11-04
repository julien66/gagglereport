<?php

namespace Drupal\core_event_dispatcher\Event\File;

use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;

/**
 * Class ArchiverInfoAlterEvent.
 */
#[HookEvent(id: 'archiver_info_alter', alter: 'archiver_info')]
class ArchiverInfoAlterEvent extends PluginDefinitionAlterEventBase {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FileHookEvents::ARCHIVER_INFO_ALTER;
  }

}
