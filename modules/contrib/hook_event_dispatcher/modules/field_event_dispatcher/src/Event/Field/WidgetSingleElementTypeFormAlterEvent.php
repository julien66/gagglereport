<?php

namespace Drupal\field_event_dispatcher\Event\Field;

use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class WidgetSingleElementTypeFormAlterEvent.
 */
#[HookEvent(id: 'widget_single_element_type_form_alter', alter: 'field_widget_single_element_form')]
class WidgetSingleElementTypeFormAlterEvent extends WidgetSingleElementFormAlterEvent {

  /**
   * The field widget instance.
   *
   * @var \Drupal\Core\Field\WidgetInterface
   */
  private readonly WidgetInterface $widget;

  /**
   * {@inheritdoc}
   */
  public function __construct(array &$element, FormStateInterface $formState, array $context) {
    parent::__construct($element, $formState, $context);
    $this->widget = $context['widget'];
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return 'hook_event_dispatcher.widget_single_element_' . $this->widget->getPluginId() . '.alter';
  }

}
