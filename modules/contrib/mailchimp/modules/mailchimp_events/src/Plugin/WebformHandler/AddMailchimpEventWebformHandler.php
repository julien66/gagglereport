<?php

namespace Drupal\mailchimp_events\Plugin\WebformHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\mailchimp_events\Entity\MailchimpEvent;
use Drupal\webform\Plugin\WebformHandlerBase;
use Drupal\webform\WebformSubmissionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Webform submission Event handler.
 *
 * @WebformHandler(
 *   id = "add_mailchimp_event",
 *   label = @Translation("Add Mailchimp Event"),
 *   category = @Translation("Mailchimp"),
 *   description = @Translation("Trigger a Mailchimp Event on a submission."),
 *   cardinality = \Drupal\webform\Plugin\WebformHandlerInterface::CARDINALITY_UNLIMITED,
 *   results = \Drupal\webform\Plugin\WebformHandlerInterface::RESULTS_PROCESSED,
 *   submission = \Drupal\webform\Plugin\WebformHandlerInterface::SUBMISSION_OPTIONAL,
 *   tokens = TRUE,
 * )
 */
class AddMailchimpEventWebformHandler extends WebformHandlerBase {

  /**
   * The Mailchimp API service.
   *
   * @var \Drupal\mailchimp\ApiService
   */
  protected $apiService;

  /**
   * The webform token manager.
   *
   * @var \Drupal\webform\WebformTokenManagerInterface
   */
  protected $tokenManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->apiService = $container->get('mailchimp.api');
    $instance->tokenManager = $container->get('webform.token_manager');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getSummary() {
    $configuration = $this->getConfiguration();
    $settings = $configuration['settings'];
    $list = mailchimp_get_list($settings['list']);
    $settings['list'] = $list ? $list->name : '';
    $settings['event_name'] = $this->getEventName($settings['event_name']);
    $settings['event_value'] = $settings['properties'];

    return [
      '#settings' => $settings,
    ] + parent::getSummary();
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'list' => '',
      'email_type' => 'user',
      'email' => '',
      'event_name' => '',
      'properties' => '',
      'is_syncing' => FALSE,
      'debug' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $mc_lists = $this->apiService->getAudiences();
    $list_options = [];

    foreach ($mc_lists as $key => $value) {
      $list_options[$key] = $value->name;
    }

    $events = MailchimpEvent::loadMultiple();
    foreach ($events as $key => $event) {
      $event_options[$event->id()] = $event->getName();
    }

    $form['events'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Events'),
    ];

    $form['events']['list'] = [
      '#type' => 'select',
      '#title' => $this->t('Mailchimp audience'),
      '#weight' => '0',
      '#required' => TRUE,
      '#options' => $list_options,
      '#default_value' => $this->configuration['list'] ?: FALSE,
    ];

    // Options:
    // Logged-in user email address
    // An email field on the form
    // A token
    // A static value.
    $form['events']['email_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Email'),
      '#description' => $this->t('Which email address to send'),
      '#options' => [
        'user' => $this->t('The logged in user'),
        'form_email' => $this->t('An email on the form'),
        'single' => $this->t('A specific email address'),
        'token' => $this->t('A token'),
      ],
      '#weight' => '0',
      '#required' => TRUE,
      '#default_value' => $this->configuration['email_type'] ?: 'user',
    ];
    $form_emails = [];
    $elements = $this->webform->getElementsInitializedAndFlattened();
    foreach ($elements as $element_key => $element) {
      if ($element['#type'] == 'email') {
        $form_emails['[webform_submission:values:' . $element_key . ']'] = $element_key . ':"' . $element['#title'] . '"';
      }
    }

    $form['events']['form_email'] = [
      '#type' => 'select',
      '#title' => $this->t('Email'),
      '#description' => $this->t('An email address element on the webform.'),
      '#options' => $form_emails,
      '#weight' => '0',
      '#states' => [
        'visible' => [
          ':input[name="settings[email_type]"]' => ['value' => 'form_email'],
        ],
      ],
      '#default_value' => ($this->configuration['email'] && $this->configuration['email_type'] == 'form_email') ? $this->configuration['email'] : '',
    ];

    $form['events']['single'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('The email address to associate with this event.'),
      '#weight' => '0',
      '#states' => [
        'visible' => [
          ':input[name="settings[email_type]"]' => ['value' => 'single'],
        ],
      ],
      '#default_value' => ($this->configuration['email'] && $this->configuration['email_type'] == 'single') ? $this->configuration['email'] : '',
    ];

    $form['events']['token'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Token'),
      '#description' => $this->t('A token containing the email address to associate with this event.'),
      '#weight' => '0',
      '#states' => [
        'visible' => [
          ':input[name="settings[email_type]"]' => ['value' => 'token'],
        ],
      ],
      '#element_validate' => ['token_element_validate'],
      '#default_value' => ($this->configuration['email'] && $this->configuration['email_type'] == 'token') ? $this->configuration['email'] : '',
    ];

    // If the event has been deleted, the event name is no longer valid.
    $add_link = Link::createFromRoute('You can add Events here.', 'entity.mailchimp_event.add_form', [], ['attributes' => ['target' => '_blank']]);
    $form['events']['event_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#description' => $this->t('The name of the Event. %add_link', ['%add_link' => $add_link->toString()]),
      '#weight' => '0',
      '#required' => TRUE,
      '#options' => $event_options,
      '#default_value' => $this->configuration['event_name'] ?: FALSE,
      '#ajax' => [
        'callback' => [$this, 'getMailchimpEventProperty'],
        'disable-refocus' => FALSE,
        'event' => 'change',
        'wrapper' => 'property-holder',
        'progress' => [
          'type' => 'throbber',
          'message' => $this->t('Verifying entry...'),
        ],
      ],
    ];
    $form['events']['event_value'] = [
      '#type' => 'details',
      '#title' => $this->t('Properties'),
      '#description' => $this->t('Properties can be any string. This field supports tokens.'),
      '#open' => TRUE,
      '#prefix' => '<div id="property-holder">',
      '#suffix' => '</div>',
    ];

    if (isset($form_state->getValues()['event_name'])) {
      $event_id = $form_state->getValues()['event_name'];
    }
    elseif ($this->configuration['event_name'] && $this->isValidEventId($this->configuration['event_name'])) {
      $event_id = $this->configuration['event_name'];
    }
    else {
      $event_id = array_key_first($event_options);
    }

    $form['events']['event_value']['properties'] = $this->getEventPropertiesById($event_id);

    $form['events']['is_syncing'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Suppress automations in Mailchimp?'),
      '#description' => $this->t('Events will be logged, but automations will not trigger.'),
      '#default_value' => $this->configuration['is_syncing'] ?? FALSE,
    ];

    // Development.
    $form['development'] = [
      '#type' => 'details',
      '#title' => $this->t('Development settings'),
    ];
    $form['development']['debug'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable debugging'),
      '#description' => $this->t('If checked, trigger events will be displayed onscreen to all users.'),
      '#return_value' => TRUE,
      '#default_value' => $this->configuration['debug'],
    ];

    $this->elementTokenValidate($form);

    return $this->setSettingsParents($form);
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->hasAnyErrors()) {
      return;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);
    $this->applyFormStateToConfiguration($form_state);
    $this->configuration['email'] = $this->getEmailToken($form_state->getValues());
    $this->configuration['properties'] = $form_state->getValue('events')['event_value']['properties'];
  }

  /**
   * Finds the token for the email to send.
   *
   * @return string
   *   The token.
   */
  protected function getEmailToken($values) {
    $type = $values['email_type'];
    $email = FALSE;

    switch ($type) {
      case 'user':
        $email = '[current-user:mail]';
        break;

      case 'form_email':
        $email = $values['events']['form_email'];
        break;

      case 'single':
        $email = $values['events']['single'];
        break;

      case 'token':
        $email = $values['events']['token'];
        break;
    }
    return $email;
  }

  /**
   * Finds the the email to send.
   *
   * @return string
   *   The email value.
   */
  protected function getEmail() {
    return $this->replaceTokens($this->configuration['email'], $this->getWebformSubmission(), [], ['clear' => TRUE]);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(WebformSubmissionInterface $webform_submission, $update = TRUE) {
    $email = $this->getEmail();

    $list = $this->configuration['list'];

    $properties = $this->configuration['properties'];
    $send_properties = [];

    if ($properties) {
      foreach ($properties as $key => $value) {
        $send_properties[$key] = $this->replaceTokens($value, $this->getWebformSubmission(), [], ['clear' => TRUE]);
      }
    }

    $event_name = $this->getEventName($this->configuration['event_name']);
    $is_syncing = $this->configuration['is_syncing'];

    $result = mailchimp_events_add_member_event($list, $email, $event_name, $send_properties, $is_syncing);
    if ($this->configuration['debug']) {
      $debug = $this->t("Called function: mailchimp_events_add_member_event(%list, %email, %event_name, %properties, %is_syncing).", [
        '%list' => $list,
        '%email' => $email,
        '%event_name' => $event_name,
        '%properties' => print_r($send_properties, TRUE),
        '%is_syncing' => $is_syncing,
      ]);

      if ($result !== FALSE) {
        $this->messenger()->addStatus($debug);
      }
      else {
        $this->messenger()->addError($debug);
        $this->messenger()->addError($this->t('Member event add failed. Check the <a href=":watchdog">logs for Mailchimp</a>', [':watchdog' => Url::fromRoute('dblog.overview')->toString()]));
      }
    }
  }

  /**
   * Gets the MailchimpActon's Name..
   *
   * @param int $id
   *   The Mailchimp Event ID.
   *
   * @return string
   *   The name of the event.
   */
  protected function isValidEventId($id) {
    return MailchimpEvent::load($id);
  }

  /**
   * Gets the MailchimpActon's Name..
   *
   * @param int $id
   *   The Mailchimp Event ID.
   *
   * @return string
   *   The name of the event.
   */
  protected function getEventName($id) {
    $chosen_event = MailchimpEvent::load($id);
    // Add a line for each property with a place to enter a value.
    return $chosen_event ? $chosen_event->getName() : '';
  }

  /**
   * Gets the Mailchimp Event properties, given the entity's ID.
   *
   * @param int $id
   *   The Mailchimp Event ID.
   *
   * @return array
   *   A set of form fields for each property on the entity.
   */
  protected function getEventPropertiesById($id) {
    $property_fields = [];
    // Load all the properties of the event name.
    $chosen_event = MailchimpEvent::load($id);
    // Add a line for each property with a place to enter a value.
    $properties = $chosen_event ? $chosen_event->getProperties() : [];

    foreach ($properties as $property) {
      $property_fields[$property['value']] = [
        '#type' => 'textfield',
        '#title' => $property['value'],
        '#weight' => '0',
        '#default_value' => $this->configuration['properties'][$property['value']] ?? '',
      ];
    }

    return $property_fields;
  }

  /**
   * Ajax call to return the event_value part of the form.
   *
   * @param array $form
   *   The form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form_state.
   *
   * @return array
   *   The event_value part of the form.
   */
  public function getMailchimpEventProperty(array &$form, FormStateInterface $form_state) {
    // Return the prepared textfield.
    return $form['settings']['events']['event_value'];
  }

}
