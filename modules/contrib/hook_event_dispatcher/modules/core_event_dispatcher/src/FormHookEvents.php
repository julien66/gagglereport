<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for form hooks.
 */
final class FormHookEvents {

  /**
   * Perform alterations before a form is rendered.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Form\FormAlterEvent
   * @see core_event_dispatcher_form_alter()
   * @see hook_form_alter()
   *
   * @var string
   */
  public const FORM_ALTER = HookEventDispatcherInterface::PREFIX . 'form.alter';

}
