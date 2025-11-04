<?php

namespace Drupal\webform_event_dispatcher\Event\WebformElement;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class WebformElementTypeAlterEvent.
 */
#[HookEvent(id: 'webform_element_type_alter', alter: 'webform_element')]
class WebformElementTypeAlterEvent extends WebformElementAlterEvent {

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    $type = $this->getElementType();
    return 'hook_event_dispatcher.webform.element_' . $type . '.alter';
  }

}
