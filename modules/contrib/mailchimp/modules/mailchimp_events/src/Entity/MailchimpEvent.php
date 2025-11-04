<?php

namespace Drupal\mailchimp_events\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Mailchimp event entity.
 *
 * @ingroup mailchimp_events
 *
 * @ContentEntityType(
 *   id = "mailchimp_event",
 *   label_singular = @Translation("event type"),
 *   label_plural = @Translation("event types"),
 *   label_collection = @Translation("Mailchimp Event Types"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\mailchimp_events\MailchimpEventListBuilder",
 *     "views_data" = "Drupal\mailchimp_events\Entity\MailchimpEventViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\mailchimp_events\Form\MailchimpEventForm",
 *       "add" = "Drupal\mailchimp_events\Form\MailchimpEventForm",
 *       "edit" = "Drupal\mailchimp_events\Form\MailchimpEventForm",
 *       "delete" = "Drupal\mailchimp_events\Form\MailchimpEventDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\mailchimp_events\MailchimpEventHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\mailchimp_events\MailchimpEventAccessControlHandler",
 *   },
 *   base_table = "mailchimp_event",
 *   translatable = FALSE,
 *   admin_permission = "administer mailchimp event types",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/config/services/mailchimp/event/{mailchimp_event}",
 *     "add-form" = "/admin/config/services/mailchimp/event/add",
 *     "edit-form" = "/admin/config/services/mailchimp/event/{mailchimp_event}/edit",
 *     "delete-form" = "/admin/config/services/mailchimp/event/{mailchimp_event}/delete",
 *     "collection" = "/admin/config/services/mailchimp/event",
 *   }
 * )
 */
class MailchimpEvent extends ContentEntityBase implements MailchimpEventInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getProperties() {
    return $this->get('properties')->getValue();
  }

  /**
   * {@inheritdoc}
   */
  public function setProperties($properties) {
    $this->set('properties', $properties);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the event type must only contain letters, numbers, underscores, and dashes.'))
      ->setSettings([
        'max_length' => 30,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['properties'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Properties'))
      ->setDescription(t('The properties of the event type, which can be used to pass data to Mailchimp and provide context. May only contain letters, numbers, underscores, and dashes.'))
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setCardinality('-1')
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the event type was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the event type was last edited.'));

    return $fields;
  }

}
