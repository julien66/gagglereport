<?php

namespace Drupal\field_event_dispatcher\Event\Field;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FormatterInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class FieldFormatterThirdPartySettingsFormEvent.
 */
#[HookEvent(id: 'field_formatter_third_party_settings_form')]
class FieldFormatterThirdPartySettingsFormEvent extends AbstractFieldThirdPartySettingsFormEvent {

  /**
   * FieldFormatterThirdPartySettingsFormEvent constructor.
   *
   * @param \Drupal\Core\Field\FormatterInterface $plugin
   *   The instantiated field formatter plugin.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $fieldDefinition
   *   The field definition.
   * @param string $viewMode
   *   The entity view mode.
   * @param array $form
   *   The (entire) configuration form array.
   * @param \Drupal\Core\Form\FormStateInterface $formState
   *   The form state.
   */
  public function __construct(
    private readonly FormatterInterface $plugin,
    FieldDefinitionInterface $fieldDefinition,
    private readonly string $viewMode,
    array $form,
    FormStateInterface $formState,
  ) {
    $this->fieldDefinition = $fieldDefinition;
    $this->form = $form;
    $this->formState = $formState;
  }

  /**
   * Get the instantiated field formatter plugin.
   *
   * @return \Drupal\Core\Field\FormatterInterface
   *   A field formatter plugin.
   */
  public function getPlugin(): FormatterInterface {
    return $this->plugin;
  }

  /**
   * Get the entity view mode.
   *
   * @return string
   *   The current view mode.
   */
  public function getViewMode(): string {
    return $this->viewMode;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FieldHookEvents::FIELD_FORMATTER_THIRD_PARTY_SETTINGS_FORM;
  }

}
