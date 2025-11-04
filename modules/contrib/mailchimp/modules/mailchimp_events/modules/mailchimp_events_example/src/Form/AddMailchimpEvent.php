<?php

namespace Drupal\mailchimp_events_example\Form;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\mailchimp_events\Entity\MailchimpEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A sample form for adding a Mailchimp Event.
 */
class AddMailchimpEvent extends FormBase {

  /**
   * The Mailchimp API service.
   *
   * @var \Drupal\mailchimp\ApiService
   */
  protected $apiService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->apiService = $container->get('mailchimp.api');
    return $instance;
  }
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'add_mailchimp_event';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description']['#markup'] = $this->t(
      'Cause an event to occur now, for a specific audience member in Mailchimp.'
    );

    $add_link = Link::createFromRoute(
      'You can add event types here.',
      'entity.mailchimp_event.add_form',
      [],
      ['attributes' => ['target' => '_blank']]
    );

    $mc_lists = $this->apiService->getAudiences();
    $list_options = [];

    foreach ($mc_lists as $key => $value) {
      $list_options[$key] = $value->name;
    }

    $events = MailchimpEvent::loadMultiple();
    $event_options = [];
    if (empty($events)) {
      $this->messenger()->addError($this->t('At least one event type is required. @add-link', [
        '@add-link' => $add_link->toString(),
      ]));
      return $form;
    }
    else {
      foreach ($events as $event) {
        $event_options[$event->getName()] = $event->getName();
      }
    }

    $form['list'] = [
      '#type' => 'select',
      '#title' => $this->t('Mailchimp audience'),
      '#weight' => '0',
      '#required' => TRUE,
      '#options' => $list_options,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#description' => $this->t('The email address to associate with this event.'),
      '#weight' => '0',
      '#required' => TRUE,
    ];

    $form['event_name'] = [
      '#type' => 'select',
      '#title' => $this->t('Event Name'),
      '#description' => $this->t('The name of the Event. %add_link', ['%add_link' => $add_link->toString()]),
      '#weight' => '0',
      '#required' => TRUE,
      '#options' => $event_options,
    ];
    $form['event_value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Event value'),
      '#description' => $this->t('This can be any string.'),
      '#maxlength' => 64,
      '#size' => 64,
      '#weight' => '0',
      '#required' => TRUE,
    ];

    $form['is_syncing'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Is Syncing?'),
      '#description' => $this->t('Events created with the is_syncing value set to true will not trigger automations.'),
    ];

    $form['occurred_at'] = [
      '#type' => 'datetime',
      '#description' => $this->t('The date and time the event occurred. Defaults to the time when the form loaded.'),
      '#title' => $this->t('Occurred at'),
      '#size' => 20,
      '#date_time_format' => 'H:i',
      '#default_value' => DrupalDateTime::createFromTimestamp(time()),
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $email = $form_state->getValue('email');
    $list = $form_state->getValue('list');
    $properties = [
      'value' => $form_state->getValue('event_value'),
      'another_property' => 'Always the same!',
    ];
    $event_name = $form_state->getValue('event_name');
    $occurred_at = $form_state->getValue('occurred_at')->getTimestamp();
    $is_syncing = $form_state->getValue('is_syncing');

    $result = mailchimp_events_add_member_event($list, $email, $event_name, $properties, $is_syncing, $occurred_at);
    $debug = $this->t("Called function: mailchimp_events_add_member_event(%list, %email, %event_name, %properties, %is_syncing, %occurred_at).", [
      '%list' => $list,
      '%email' => $email,
      '%event_name' => $event_name,
      '%properties' => print_r($properties, TRUE),
      '%is_syncing' => $is_syncing,
      '%occurred_at' => $occurred_at,
    ]);

    if ($result !== FALSE) {
      $this->messenger()->addStatus($debug);
    }
    else {
      $this->messenger()->addError($debug);
      $this->messenger()->addError($this->t('No results returned. Check the <a href=":watchdog">logs for Mailchimp</a>', [':watchdog' => Url::fromRoute('dblog.overview')->toString()]));
    }
  }

}
