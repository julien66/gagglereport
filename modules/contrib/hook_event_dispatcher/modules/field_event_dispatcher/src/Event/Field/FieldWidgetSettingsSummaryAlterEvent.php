<?php

namespace Drupal\field_event_dispatcher\Event\Field;

use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class FieldWidgetSettingsSummaryAlterEvent.
 */
#[HookEvent(id: 'field_widget_settings_summary_alter')]
class FieldWidgetSettingsSummaryAlterEvent extends AbstractFieldSettingsSummaryFormEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FieldHookEvents::FIELD_WIDGET_SETTINGS_SUMMARY_ALTER;
  }

}
