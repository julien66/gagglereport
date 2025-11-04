<?php

namespace Drupal\field_event_dispatcher\Event\Field;

use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class FieldFormatterSettingsSummaryAlterEvent.
 */
#[HookEvent(id: 'field_formatter_settings_summary_alter')]
class FieldFormatterSettingsSummaryAlterEvent extends AbstractFieldSettingsSummaryFormEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FieldHookEvents::FIELD_FORMATTER_SETTINGS_SUMMARY_ALTER;
  }

}
