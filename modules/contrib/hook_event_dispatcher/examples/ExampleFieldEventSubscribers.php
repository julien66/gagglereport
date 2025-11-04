<?php

namespace Drupal\hook_event_dispatcher;

use Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementFormAlterEvent;
use Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementTypeFormAlterEvent;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExampleFieldEventSubscribers.
 *
 * Don't forget to define your class as a service and tag it as
 * an "event_subscriber":
 *
 * services:
 *  hook_event_dispatcher.example_field_subscribers:
 *   class: Drupal\hook_event_dispatcher\ExampleFieldEventSubscribers
 *   tags:
 *     - { name: event_subscriber }
 */
final class ExampleFieldEventSubscribers implements EventSubscriberInterface {

  /**
   * Alter widget form.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementFormAlterEvent $event
   *   The event.
   */
  public function alterWidgetForm(WidgetSingleElementFormAlterEvent $event): void {
    $element = &$event->getElement();
    $element['extra_field'] = [
      '#type' => 'textfield',
      '#title' => 'I am an extra field!',
    ];
  }

  /**
   * Alter widget string text field.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\WidgetSingleElementTypeFormAlterEvent $event
   *   The event.
   */
  public function alterWidgetStringTextField(WidgetSingleElementTypeFormAlterEvent $event): void {
    $element = &$event->getElement();
    // Do something cool.
    $element['special_label'] = [
      'type' => 'label',
      'label' => 'Extra special label',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      FieldHookEvents::WIDGET_SINGLE_ELEMENT_FORM_ALTER => 'alterWidgetForm',
      'hook_event_dispatcher.widget_single_element_string_textfield.alter' => 'alterWidgetStringTextField',
    ];
  }

}
