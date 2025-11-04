<?php

namespace Drupal\field_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for the field hooks.
 */
final class FieldHookEvents {

  /**
   * Alter forms for field widgets provided by other modules.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementFormAlterEvent
   * @see field_event_dispatcher_field_widget_single_element_form_alter()
   * @see hook_field_widget_single_element_form_alter()
   *
   * @var string
   */
  public const WIDGET_SINGLE_ELEMENT_FORM_ALTER = HookEventDispatcherInterface::PREFIX . 'widget_single_element_form.alter';

  /**
   * Alter the complete form for field widgets provided by other modules.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\WidgetCompleteFormAlterEvent
   * @see field_event_dispatcher_field_widget_complete_form_alter()
   * @see hook_field_widget_complete_form_alter()
   *
   * @var string
   */
  public const WIDGET_COMPLETE_FORM_ALTER = HookEventDispatcherInterface::PREFIX . 'widget_complete_form.alter';

  /**
   * Perform alterations on Field API formatter types.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\FieldFormatterInfoAlterEvent
   * @see field_event_dispatcher_field_formatter_info_alter()
   * @see hook_field_formatter_info_alter()
   *
   * @var string
   */
  public const FIELD_FORMATTER_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'field_formatter.info.alter';

  /**
   * Allow modules to add field formatter settings provided by other modules.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\FieldFormatterThirdPartySettingsFormEvent
   * @see field_event_dispatcher_field_formatter_third_party_settings_form()
   * @see hook_field_formatter_third_party_settings_form()
   *
   * @var string
   */
  public const FIELD_FORMATTER_THIRD_PARTY_SETTINGS_FORM = HookEventDispatcherInterface::PREFIX . 'field_formatter.third_party.settings_form';

  /**
   * Allow modules to add settings to field widgets provided by other modules.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\FieldWidgetThirdPartySettingsFormEvent
   * @see field_event_dispatcher_field_widget_third_party_settings_form()
   * @see hook_field_widget_third_party_settings_form()
   *
   * @var string
   */
  public const FIELD_WIDGET_THIRD_PARTY_SETTINGS_FORM = HookEventDispatcherInterface::PREFIX . 'field_widget.third_party.settings_form';

  /**
   * Alters the field formatter settings summary.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\FieldFormatterSettingsSummaryAlterEvent
   * @see field_event_dispatcher_field_formatter_settings_summary_alter()
   * @see hook_field_formatter_settings_summary_alter()
   *
   * @var string
   */
  public const FIELD_FORMATTER_SETTINGS_SUMMARY_ALTER = HookEventDispatcherInterface::PREFIX . 'field_formatter.settings_summary.alter';

  /**
   * Alters the field widget settings summary.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\FieldWidgetSettingsSummaryAlterEvent
   * @see field_event_dispatcher_field_widget_settings_summary_alter()
   * @see hook_field_widget_settings_summary_alter()
   *
   * @var string
   */
  public const FIELD_WIDGET_SETTINGS_SUMMARY_ALTER = HookEventDispatcherInterface::PREFIX . 'field_widget.settings_summary.alter';

  /**
   * Alters the field plugin type.
   *
   * @Event
   *
   * @see \Drupal\field_event_dispatcher\Event\Field\FieldInfoAlterEvent
   * @see field_event_dispatcher_field_info_alter()
   * @see hook_field_info_alter()
   *
   * @var string
   */
  public const FIELD_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'field.info.alter';

}
