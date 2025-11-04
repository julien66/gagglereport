<?php

/**
 * @file
 * Mailchimp module hook definitions.
 */

use Drupal\Core\Entity\EntityInterface;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Performs an action upon successfully subscribing to an audience.
 *
 * @param string $list_id
 *   The Mailchimp audience ID.
 * @param string $email
 *   The email address subscribed.
 * @param string $merge_vars
 *   The mergevars used during the subscription.
 *
 * @ingroup mailchimp
 */
function hook_mailchimp_subscribe_success($list_id, $email, $merge_vars) {
}

/**
 * Performs an action upon successfully unsubscribing from an audience.
 *
 * @param string $list_id
 *   The Mailchimp audience ID.
 * @param string $email
 *   The email address unsubscribed.
 *
 * @ingroup mailchimp
 */
function hook_mailchimp_unsubscribe_success($list_id, $email) {
}

/**
 * Perform an action during the firing of a Mailchimp webhook.
 *
 * Refer to http://apidocs.mailchimp.com/webhooks for more details.
 *
 * @param string $type
 *   The type of webhook firing.
 * @param array $data
 *   The data contained in the webhook.
 *
 * @ingroup mailchimp
 */
function hook_mailchimp_process_webhook($type, array $data) {
}

/**
 * Alter mergevars before they are sent to Mailchimp.
 *
 * @param array $mergevars
 *   The current mergevars.
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   The entity used to populate the mergevars.
 * @param string $entity_type
 *   The entity type.
 *
 * @ingroup mailchimp
 */
function hook_mailchimp_lists_mergevars_alter(array &$mergevars, EntityInterface $entity, $entity_type) {
}

/**
 * Alter interest groups before they are sent to Mailchimp.
 *
 * @param array $interests
 *   The current interest groups.
 * @param \Drupal\Core\Entity\EntityInterface $entity
 *   The entity used to populate the interest groups.
 * @param array $choices
 *   The entity form submission data.
 *
 * @ingroup mailchimp
 */
function hook_mailchimp_lists_interest_groups_alter(array &$interests, EntityInterface $entity, array $choices) {
}

/**
 * Alter campaign recipients and template.
 *
 * @param object $recipients
 *   Audience settings for the campaign.
 * @param array $template
 *   Associative array of template content indexed by section ID.
 * @param string $campaign_id
 *   The ID of the campaign to save, if updating.
 *
 * @ingroup mailchimp
 */
function hook_mailchimp_campaign_alter(object &$recipients, array &$template, string $campaign_id) {
}

/**
 * Alter campaign template and content.
 *
 * @param array $template
 *   Associative array of template content indexed by section ID.
 * @param array $content
 *   Associative array of filtered template content indexed by section ID.
 * @param string $campaign_id
 *   The ID of the campaign to save, if updating.
 *
 * @ingroup mailchimp
 */
function hook_mailchimp_campaign_content_alter(array &$template, array &$content, string $campaign_id) {
}

/**
 * @} End of "addtogroup hooks".
 */
