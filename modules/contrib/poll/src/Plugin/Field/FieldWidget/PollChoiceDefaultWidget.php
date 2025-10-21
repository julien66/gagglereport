<?php

namespace Drupal\poll\Plugin\Field\FieldWidget;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'poll_choice_default' widget.
 *
 * @FieldWidget(
 *   id = "poll_choice_default",
 *   module = "poll",
 *   label = @Translation("Poll choice"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class PollChoiceDefaultWidget extends WidgetBase {

  /**
   * The default value of a vote.
   */
  const VOTE_DEFAULT_VALUE = 1;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->entityTypeManager = $container->get('entity_type.manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $langcode = $this->getCurrentLangcode($form_state, $items);

    /** @var \Drupal\poll\PollChoiceInterface $choice */
    $choice = $items[$delta]->entity;
    if ($choice) {
      // If target translation is not yet available, populate it with data from
      // the original choice.
      if ($choice->language()->getId() != $langcode && !$choice->hasTranslation($langcode)) {
        $choice->addTranslation($langcode, $choice->toArray());
      }

      // Initiate the choice with the correct translation.
      $choice = $choice->getTranslation($langcode);
    }

    $element['target_id'] = [
      '#type' => 'value',
      '#value' => $choice ? $choice->id() : NULL,
    ];
    $element['langcode'] = [
      '#type' => 'value',
      '#value' => $langcode,
    ];

    $element['choice'] = [
      '#type' => 'textarea',
      '#rows' => 1,
      '#placeholder' => $this->t('Choice'),
      '#empty_value' => '',
      '#default_value' => $choice ? $choice->choice->value : NULL,
      '#prefix' => '<div class="container-inline">',
    ];
    return $element;
  }

  /**
   * Gets current language code from the form state or item.
   *
   * Since the choice field is not set as translatable, the item language
   * code is set to the source language. The intended translation language
   * is only accessibly through the form state.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field items object.
   *
   * @return string
   *   The language code to be used.
   */
  protected function getCurrentLangcode(FormStateInterface $form_state, FieldItemListInterface $items) {
    return $form_state->get('langcode') ?: $items->getEntity()->language()->getId();
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    return $field_definition->getTargetEntityTypeId() == 'poll' && $field_definition->getName() == 'choice';
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    foreach ($values as $delta => &$item_values) {

      $storage = $this->entityTypeManager->getStorage('poll_choice');
      $langcode = $item_values['langcode'];

      // Remove empty values. Removed choices will be deleted automatically.
      if (empty($item_values['choice'])) {
        unset($values[$delta]);
        continue;
      }

      /** @var \Drupal\poll\PollChoiceInterface $choice */
      $choice = !empty($item_values['target_id']) ? $storage->load($item_values['target_id']) : $storage->create(['langcode' => $langcode]);

      // If target translation is not yet available, populate it with data from
      // the original choice.
      if ($choice->language()->getId() != $langcode && !$choice->hasTranslation($langcode)) {
        $choice->addTranslation($langcode, $choice->toArray());
      }

      // Initiate the choice with the correct translation.
      $choice = $choice->getTranslation($langcode);

      // If the choice is new or changed, resave it.
      if ($choice->isNew() || $item_values['choice'] != $choice->choice->value) {
        $choice->choice->value = $item_values['choice'];
        $choice->needsSaving(TRUE);

      }
      unset($item_values['target_id'], $item_values['choice'], $item_values['langcode']);

      $item_values['entity'] = $choice;
    }
    return $values;
  }

  /**
   * {@inheritdoc}
   */
  public function form(FieldItemListInterface $items, array &$form, FormStateInterface $form_state, $get_delta = NULL) {
    $elements = parent::form($items, $form, $form_state, $get_delta);

    // Signal to content_translation that this field should be treated as
    // multilingual and not be hidden, see
    // \Drupal\content_translation\ContentTranslationHandler::entityFormSharedElements().
    $elements['#multilingual'] = TRUE;
    return $elements;
  }

}
