<?php

namespace Drupal\poll\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\poll\PollChoiceInterface;

/**
 * Defines the poll choice entity class.
 *
 * @ContentEntityType(
 *   id = "poll_choice",
 *   label = @Translation("Poll Choice"),
 *   base_table = "poll_choice",
 *   data_table = "poll_choice_field_data",
 *   admin_permission = "administer polls",
 *   content_translation_ui_skip = TRUE,
 *   translatable = TRUE,
 *   content_translation_metadata = "Drupal\poll\PollChoiceTranslationMetadataWrapper",
 *   handlers = {
 *     "access" = "\Drupal\poll\PollChoiceAccessControlHandler",
 *     "translation" = "Drupal\poll\PollChoiceTranslationHandler",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "choice",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode"
 *   }
 * )
 */
class PollChoice extends ContentEntityBase implements PollChoiceInterface {

  /**
   * Whether or not the choice must be saved when the poll is saved.
   *
   * @var bool
   */
  protected $needsSave = NULL;

  /**
   * {@inheritdoc}
   */
  public function setChoice($question) {
    $this->set('choice', $question);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function needsSaving($new_value = NULL) {
    // If explicitly set, return that value. otherwise fall back to isNew(),
    // saving is always required for new entities.
    $return = $this->needsSave ?? $this->isNew();

    if ($new_value !== NULL) {
      $this->needsSave = $new_value;
    }

    return $return;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Choice ID'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setReadOnly(TRUE);

    $fields['choice'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Choice'))
      ->setRequired(TRUE)
      ->setTranslatable(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textarea',
        'weight' => 0,
        'settings' => [
          'rows' => 1,
        ],
      ]);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The poll language code.'));

    return $fields;
  }

}
