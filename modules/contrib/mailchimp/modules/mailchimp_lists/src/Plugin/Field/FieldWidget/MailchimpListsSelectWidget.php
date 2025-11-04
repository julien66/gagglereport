<?php

namespace Drupal\mailchimp_lists\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mailchimp_lists\Plugin\Field\FieldType\MailchimpListsSubscription;

/**
 * Plugin implementation of the 'mailchimp_lists_select' widget.
 *
 * @FieldWidget (
 *   id = "mailchimp_lists_select",
 *   label = @Translation("Subscription form"),
 *   field_types = {
 *     "mailchimp_lists_subscription"
 *   },
 *   settings = {
 *     "placeholder" = "Select a Mailchimp Audience."
 *   }
 * )
 */
class MailchimpListsSelectWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\mailchimp_lists\Plugin\Field\FieldType\MailchimpListsSubscription $instance */
    $instance = $items[0];

    $email = $this->getEmail($instance);
    $mailchimp_list_id = $this->fieldDefinition->getSetting('mc_list_id');
    $at_least_one_interest_group = !empty(mailchimp_get_list($mailchimp_list_id)->intgroups);
    $hide_subscribe_checkbox = $this->fieldDefinition->getSetting('hide_subscribe_checkbox');

    $element += [
      '#title' => Html::escape($element['#title']),
      '#type' => 'fieldset',
    ];

    $element = $this->setupSubscribeCheckbox($element, $form_state, $instance, $email, $hide_subscribe_checkbox, $at_least_one_interest_group, $mailchimp_list_id);
    $element = $this->setupInterestGroups($element, $form_state, $instance, $email, $hide_subscribe_checkbox, $at_least_one_interest_group, $mailchimp_list_id);
    $element = $this->setupUnsubscribeCheckbox($element, $form_state, $instance, $email, $hide_subscribe_checkbox, $at_least_one_interest_group, $mailchimp_list_id);
    $element = $this->setupSubscriptionPendingMessage($element, $instance, $email);

    return ['value' => $element];
  }

  /**
   * Gets an email address.
   *
   * @param \Drupal\mailchimp_lists\Plugin\Field\FieldType\MailchimpListsSubscription $instance
   *   Parameter instance.
   *
   * @return bool|null|string
   *   Returns an email address.
   */
  protected function getEmail(MailchimpListsSubscription $instance) {
    $email = NULL;
    if (!empty($instance->getEntity())) {
      $email = mailchimp_lists_load_email($instance, $instance->getEntity(), FALSE);
    }
    return $email;
  }

  /**
   * Sets up a subscribe checkbox.
   *
   * @param array $element
   *   Parameter element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Parameter form_state interface.
   * @param \Drupal\mailchimp_lists\Plugin\Field\FieldType\MailchimpListsSubscription $instance
   *   Parameter instance.
   * @param mixed $email
   *   Parameter email.
   * @param bool $hide_subscribe_checkbox
   *   Parameter hide subscribe checkbox.
   * @param bool $at_least_one_interest_group
   *   Parameter at least one interest group.
   * @param string $mailchimp_list_id
   *   Mailchimp audience ID.
   *
   * @return array
   *   Returns array element.
   */
  protected function setupSubscribeCheckbox(array $element, FormStateInterface $form_state, MailchimpListsSubscription $instance, $email, $hide_subscribe_checkbox, $at_least_one_interest_group, $mailchimp_list_id) {
    $memberStatus = $this->GetMemberStatus($instance, $email);
    if ($memberStatus == 'pending') {
      // If member status is pending then don't show the subscribe
      // checkbox. The user has already attempted to subscribe and
      // must check their email to complete the subscription process.
      // That means they are neither fully subscribed nor unsubscribed.
      return $element;
    }

    $subscribe_default = $this->getSubscribeDefault($instance, $email);
    $subscribe_checkbox_label = $this->fieldDefinition->getSetting('subscribe_checkbox_label');
    $element['subscribe'] = [
      '#title' => $subscribe_checkbox_label ?: $this->t('Subscribe'),
      '#type' => 'checkbox',
      '#default_value' => ($subscribe_default) ? TRUE : $this->fieldDefinition->isRequired(),
      '#required' => $this->fieldDefinition->isRequired(),
      '#disabled' => $this->fieldDefinition->isRequired(),
    ];
    $showSubscribeCheckbox = $this->subscribeCheckboxShown($form_state, $hide_subscribe_checkbox, $at_least_one_interest_group, $email, $mailchimp_list_id);
    if ($showSubscribeCheckbox) {
      $element['subscribe']['#access'] = TRUE;
    }
    else {
      $element['subscribe']['#access'] = FALSE;
    }
    return $element;
  }

  /**
   * Sets up interest groups.
   *
   * @param array $element
   *   Parameter array element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Parameter form state interface.
   * @param mixed $instance
   *   Parameter instance.
   * @param mixed $email
   *   Parameter email.
   * @param mixed $hide_subscribe_checkbox
   *   Parameter hide subscribe checkbox.
   * @param mixed $at_least_one_interest_group
   *   Parameter at least one interest group.
   * @param mixed $mailchimp_list_id
   *   Mailchimp audience ID.
   *
   * @return array
   *   Returns array element.
   */
  protected function setupInterestGroups(array $element, FormStateInterface $form_state, $instance, $email, $hide_subscribe_checkbox, $at_least_one_interest_group, $mailchimp_list_id) {
    $interest_groups_label = $instance->getFieldDefinition()->getSetting('interest_groups_label');
    $instance_name = $instance->getFieldDefinition()->getName();
    $instance_list_id = $instance->getFieldDefinition()->getSetting('mc_list_id');
    $mc_instance_list = mailchimp_get_list($instance_list_id);

    // TRUE if interest groups are enabled for this audience.
    $show_interest_groups = $this->fieldDefinition->getSetting('show_interest_groups');
    // TRUE if widget is being used to set default values via admin form.
    $is_default_value_widget = $this->isDefaultValueWidget($form_state);
    // TRUE if interest groups are enabled but hidden from the user.
    $interest_groups_hidden = $this->fieldDefinition->getSetting('interest_groups_hidden');

    $interest_group_element_type = 'fieldset';
    if (!$is_default_value_widget && $show_interest_groups && $hide_subscribe_checkbox && $at_least_one_interest_group && !$this->memberIsUnsubscribed($mailchimp_list_id, $email)) {
      $interest_group_element_type = 'container';
    }

    if ($show_interest_groups || $is_default_value_widget) {
      if ($interest_groups_hidden && !$is_default_value_widget) {
        $element['interest_groups'] = [];
      }
      else {
        $element['interest_groups'] = [
          '#type' => $interest_group_element_type,
          '#title' => Html::escape($interest_groups_label),
          '#weight' => 100,
        ];
        $element['interest_groups']['#states'] = [
          'invisible' => [
            ':input[name="' . $instance_name . '[0][value][subscribe]"]' => ['checked' => FALSE],
          ],
        ];
      }

      if ($is_default_value_widget) {
        $element['interest_groups']['#states']['visible'] = [
          ':input[name="settings[show_interest_groups]"]' => ['checked' => TRUE],
          ':input[name="default_value_input[' . $instance_name . '][0][value][subscribe]"]' => ['checked' => TRUE],
        ];
      }

      if ($this->getMemberStatus($instance, $email) == 'subscribed') {
        $groups_default = $this->getInterestGroupsDefaults($instance);
      }
      else {
        $value_array = $instance->getValue();
        // $groups_default must be an array.
        if (array_key_exists('interest_groups', $value_array) && is_array($value_array['interest_groups'])) {
          $groups_default = $value_array['interest_groups'];
        }
        else {
          $groups_default = [];
        }
      }

      if (!empty($mc_instance_list->intgroups)) {
        $mode = $is_default_value_widget ? 'admin' : ($interest_groups_hidden ? 'hidden' : 'default');
        $element['interest_groups'] += mailchimp_interest_groups_form_elements($mc_instance_list, $groups_default, $email, $mode);
      }
    }

    return $element;
  }

  /**
   * Sets up an unsubscribe checkbox.
   *
   * @param array $element
   *   Parameter array element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Parameter form state interface.
   * @param mixed $instance
   *   Parameter instance.
   * @param mixed $email
   *   Parameter email.
   * @param bool $hide_subscribe_checkbox
   *   Parameter hide subscribe checkbox.
   * @param bool $at_least_one_interest_group
   *   Parameter at least one interest group.
   * @param string $mailchimp_list_id
   *   Mailchimp audience ID.
   *
   * @return array
   *   Returns array element.
   */
  protected function setupUnsubscribeCheckbox(array $element, FormStateInterface $form_state, $instance, $email, $hide_subscribe_checkbox, $at_least_one_interest_group, $mailchimp_list_id) {

    if ($this->subscribeCheckboxShown($form_state, $hide_subscribe_checkbox, $at_least_one_interest_group, $email, $mailchimp_list_id)) {
      // When the subscribe checkbox is shown, we don't need to show
      // the unsubscribe checkbox (unchecked subscribe means the
      // same thing as unsubscribed).
      return $element;
    }

    $memberStatus = $this->GetMemberStatus($instance, $email);
    if ($memberStatus == 'subscribed') {
      $element['unsubscribe'] = [
        '#title' => $this->t("Unsubscribe"),
        '#type' => 'checkbox',
        '#weight' => 101,
        '#default_value' => FALSE,
      ];
    }

    return $element;
  }

  /**
   * Sets up subscription pending messages.
   *
   * @param array $element
   *   Parameter array element.
   * @param mixed $instance
   *   Parameter instance.
   * @param mixed $email
   *   Parameter email.
   *
   * @return array
   *   Returns element.
   */
  protected function setupSubscriptionPendingMessage(array $element, $instance, $email) {
    $memberStatus = $this->GetMemberStatus($instance, $email);
    if ($memberStatus == 'pending') {
      $element['pending'] = [
        '#type' => 'markup',
        '#markup' => $this->t("<b>Subscription is pending. Confirm by visiting your email.</b>"),
        '#weight' => 101,
      ];
    }
    return $element;
  }

  /**
   * Gets the current subscribed status.
   *
   * @param mixed $instance
   *   Parameter instance.
   * @param mixed $email
   *   Parameter email.
   *
   * @return bool
   *   The current subscribed status.
   */
  protected function getSubscribeDefault($instance, $email) {
    $subscribe_default = $instance->getSubscribe();
    if (!empty($instance->getEntity()) && $email) {
      $instance_list_id = $instance->getFieldDefinition()->getSetting('mc_list_id');
      $subscribe_default = mailchimp_is_subscribed($instance_list_id, $email);
    }
    return $subscribe_default;
  }

  /**
   * Gets current interest groups.
   *
   * @param mixed $instance
   *   Parameter instance.
   *
   * @return array
   *   The current interest groups.
   */
  protected function getInterestGroupsDefaults($instance) {
    $groups_default = $instance->getInterestGroups();

    return $groups_default;
  }

  /**
   * Gets the member status.
   *
   * @param mixed $instance
   *   Parameter instance.
   * @param mixed $email
   *   Parameter email.
   *
   * @return mixed
   *   The member status.
   */
  protected function getMemberStatus($instance, $email) {
    $memberStatus = NULL;
    if (!empty($instance->getEntity()) && $email) {
      $instance_list_id = $instance->getFieldDefinition()->getSetting('mc_list_id');
      $memberinfo = mailchimp_get_memberinfo($instance_list_id, $email, TRUE);
      if (isset($memberinfo->status)) {
        $memberStatus = $memberinfo->status;
      }
    }
    return $memberStatus;
  }

  /**
   * Determines whether to show the subscribe checkbox.
   *
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   * @param bool $hide_subscribe_checkbox
   *   Whether the field is configured to hide the Subscribe checkbox.
   * @param bool $at_least_one_interest_group
   *   Whether the audience has at least one interest group.
   * @param string $email
   *   Member email address.
   * @param string $mailchimp_list_id
   *   Mailchimp audience ID.
   *
   * @return bool
   *   Whether to show the subscribe checkbox.
   */
  protected function subscribeCheckboxShown(FormStateInterface $form_state, $hide_subscribe_checkbox, $at_least_one_interest_group, $email, $mailchimp_list_id): bool {

    // TRUE if interest groups are enabled for this audience.
    $show_interest_groups = $this->fieldDefinition->getSetting('show_interest_groups');
    // TRUE if widget is being used to set default values via admin form.
    $is_default_value_widget = $this->isDefaultValueWidget($form_state);

    // Hide the Subscribe checkbox if:
    // - The form is not being used to configure default values.
    // - The field is configured to show interest groups.
    // - The field is configured to hide the Subscribe checkbox.
    // - The audience has at least one interest group.
    // This allows users to skip the redundant step of checking the Subscribe
    // checkbox when also checking interest group checkboxes.
    $showSubscribeCheckbox = TRUE;
    if (!$is_default_value_widget && $show_interest_groups && $hide_subscribe_checkbox && $at_least_one_interest_group && $this->memberIsUnsubscribed($mailchimp_list_id, $email)) {
      $showSubscribeCheckbox = FALSE;
    }
    return $showSubscribeCheckbox;
  }

  /**
   * Shows whether a member is unsubscribed.
   *
   * @param string $mailchimp_list_id
   *   Mailchimp audience ID.
   * @param string $email
   *   Member email address.
   *
   * @return bool
   *   TRUE if the member is unsubscribed, otherwise FALSE.
   */
  protected function memberIsUnsubscribed($mailchimp_list_id, $email): bool {
    $member_info = mailchimp_get_memberinfo($mailchimp_list_id, $email);
    return (!isset($member_info->status) || $member_info->status !== "unsubscribed");
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    $is_default_value_widget = $this->isDefaultValueWidget($form_state);
    $is_new_entity = TRUE;
    if ($form_state->getFormObject() && method_exists($form_state->getFormObject(), 'getEntity') && $form_state->getFormObject()->getEntity()) {
      $is_new_entity = $form_state->getFormObject()->getEntity()->isNew();
    }

    foreach ($values as $delta => $value) {
      $new_values[$delta] = $value['value'];
      if (!$is_default_value_widget) {
        $new_values[$delta]['subscribe'] = $this->getSubscribeFromInterests($new_values[$delta], $is_new_entity);
        // Only allow us to push updates to mailchimp subscription status
        // and groups if:
        // - The entity is not new, or
        // - The entity is new and the user has checked the subscribe checkbox.
        if (!$is_new_entity || $new_values[$delta]['subscribe'] == 1) {
          $new_values[$delta]['allow_unsubscribe'] = TRUE;
        }
      }
    }

    return $new_values;
  }

  /**
   * Sets the subscribe value based on field settings and interest groups.
   *
   * @param mixed $choices
   *   The value of the field.
   * @param bool $is_new
   *   True if this is a new entity.
   *
   * @return bool
   *   TRUE if there are interests chosen on a hidden subscribe to audience
   *   checkbox.
   */
  public function getSubscribeFromInterests($choices, $is_new = FALSE) {
    $subscribe_from_interest_groups = $choices['subscribe'];
    $field_settings = $this->getFieldSettings();

    // Automatically subscribe if the field is configured to hide the
    // subscribe checkbox and at least one interest group checkbox is checked.
    if ($field_settings['show_interest_groups'] && $field_settings['hide_subscribe_checkbox']) {
      if (!empty($choices['interest_groups'])) {
        $subscribe_from_interest_groups = !$is_new;
        foreach ($choices['interest_groups'] as $group_id => $interests) {
          foreach ($interests as $interest_id => $value) {
            if (!empty($value)) {
              $subscribe_from_interest_groups = TRUE;
              break;
            }
          }
        }
      }
    }

    return $subscribe_from_interest_groups;
  }

}
