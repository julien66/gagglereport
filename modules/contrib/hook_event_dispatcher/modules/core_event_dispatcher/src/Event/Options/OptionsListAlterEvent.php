<?php

namespace Drupal\core_event_dispatcher\Event\Options;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\WidgetInterface;
use Drupal\core_event_dispatcher\OptionsHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class OptionsListAlterEvent.
 */
#[HookEvent(id: 'options_list_alter', alter: 'options_list')]
class OptionsListAlterEvent extends Event implements EventInterface {

  /**
   * The array of options for the field.
   *
   * @var array
   */
  protected array $options = [];

  /**
   * OptionsListAlterEvent constructor.
   *
   * @param array $options
   *   The array of options for the field, as returned by
   *   \Drupal\Core\TypedData\OptionsProviderInterface::getSettableOptions(). An
   *   empty option (_none) might have been added, depending on the field
   *   properties.
   * @param array $context
   *   An associative array containing:
   *   - fieldDefinition: The field definition
   *     (\Drupal\Core\Field\FieldDefinitionInterface).
   *   - entity: The entity object the field is attached to
   *     (\Drupal\Core\Entity\EntityInterface).
   *   - widget: The widget object (\Drupal\Core\Field\WidgetInterface).
   */
  public function __construct(array &$options, protected array $context) {
    $this->options = &$options;
  }

  /**
   * Gets the options for the field.
   *
   * @return array
   *   The array of options for the field.
   */
  public function &getOptions(): array {
    return $this->options;
  }

  /**
   * Gets the field definition context.
   *
   * @return \Drupal\Core\Field\FieldDefinitionInterface
   *   The field definition context.
   */
  public function getFieldDefinition(): FieldDefinitionInterface {
    return $this->context['fieldDefinition'];
  }

  /**
   * Gets the entity object the field is attached to.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The entity object the field is attached to.
   */
  public function getEntity(): EntityInterface {
    return $this->context['entity'];
  }

  /**
   * Gets the widget object.
   *
   * @return \Drupal\Core\Field\WidgetInterface|null
   *   The widget object.
   */
  public function getWidget(): ?WidgetInterface {
    return $this->context['widget'] ?? NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return OptionsHookEvents::OPTIONS_LIST_ALTER;
  }

}
