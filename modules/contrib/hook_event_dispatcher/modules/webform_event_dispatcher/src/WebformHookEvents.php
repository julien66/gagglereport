<?php

namespace Drupal\webform_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for webform hooks.
 */
final class WebformHookEvents {

  /**
   * Respond to webform elements being rendered.
   *
   * @Event
   *
   * @see \Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementAlterEvent
   * @see webform_event_dispatcher_webform_element_alter()
   * @see hook_webform_element_alter()
   *
   * @var string
   */
  public const WEBFORM_ELEMENT_ALTER = HookEventDispatcherInterface::PREFIX . 'webform.element.alter';

  /**
   * Respond to webform element info being initialized.
   *
   * @Event
   *
   * @see \Drupal\webform_event_dispatcher\Event\WebformElement\WebformElementInfoAlterEvent
   * @see webform_event_dispatcher_webform_element_info_alter()
   * @see hook_webform_element_info_alter()
   *
   * @var string
   */
  public const WEBFORM_ELEMENT_INFO_ALTER = HookEventDispatcherInterface::PREFIX . 'webform.element.info.alter';

}
