<?php

/**
 * @file
 * Rules integration for the Mailchimp Audiences module.
 */

/**
 * Implements hook_rules_action_info().
 */
function mailchimp_lists_rules_action_info() {
  $items = [];
  $items['mailchimp_lists_user_subscribe'] = [
    'label' => t('Subscribe or unsubscribe entity from a mailchimp audience'),
    'parameter' => [
      'entity' => [
        'type' => 'entity',
        'label' => t('Entity'),
        'description' => t('The entity to subscribe/unsubscribe'),
      ],
      'field' => [
        'type' => '*',
        'label' => t('Mailchimp Audience Subscription Field'),
        'description' => t('Subscription Field connected to the desired Mailchimp Audience.'),
        'restriction' => 'selector',
        'wrapped' => TRUE,
        'allow null' => FALSE,
      ],
      'subscribe' => [
        'type' => 'boolean',
        'label' => t('Subscribe'),
        'description' => t('True to subscribe, False to unsubscribe'),
      ],
    ],
    'group' => t('Mailchimp'),
    'access callback' => 'mailchimp_lists_rules_access_callback',
    'base' => 'mailchimp_lists_rules_action_entity_subscribe',
  ];
  return $items;
}

/**
 * Action callback: Subscribe an entity to a audience.
 */
function mailchimp_lists_rules_action_entity_subscribe($entity, $field, $subscribe) {
  $field_info = $field->info();
  $choices = reset($field_info['instance']['default_value']);
  $choices['subscribe'] = $subscribe;

  // Update field values in DB.
  $fieldname = $field_info['instance']->getFieldDefinition()->getName();
  $entity->$fieldname->subscribe = $choices['subscribe'];
  $entity->$fieldname->interest_groups = $choices['interest_groups'];
  $entity->save();

  mailchimp_lists_process_subscribe_form_choices($choices, $field_info['instance'], $field_info['field'], $entity->raw());
}

/**
 * Access callback for the rules integration.
 */
function mailchimp_lists_rules_access_callback() {
  return \Drupal::currentUser()->hasPermission('administer mailchimp');
}
