<?php

namespace Drupal\mailchimp_events_example\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * A sample form for listing Mailchimp Events for a member.
 */
class ListMailchimpEventsForMember extends FormBase {

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
    return 'list_mailchimp_events_for_member';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description']['#markup'] = $this->t(
      'View a list of events for a specific audience member, directly pulled from Mailchimp.'
    );

    $mc_lists = $this->apiService->getAudiences();
    $list_options = [];

    if ($events = $form_state->get('events')) {
      $listing = [];
      foreach ($events->events as $event) {
        $properties = [];
        if (isset($event->properties)) {
          foreach ($event->properties as $key => $value) {
            $properties[] = $key . ': ' . $value;
          }
        }

        $listing[] = [
          '#prefix' => $event->name ?? '',
          '#theme' => 'item_list',
          '#items' => $properties,
        ];
      }

      $form['results'] = [
        '#theme' => 'item_list',
        '#items' => $listing,
      ];
    }

    foreach ($mc_lists as $key => $value) {
      $list_options[$key] = $value->name;
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
      '#description' => $this->t('The email address of the member to view events for.'),
      '#weight' => '0',
    ];

    $form['count'] = [
      '#type' => 'number',
      '#title' => $this->t('Count'),
      '#description' => $this->t('The number of records to return. Default value is 10. Maximum value is 1000.'),
      '#default_value' => 10,
      '#max' => 1000,
    ];

    $form['offset'] = [
      '#type' => 'number',
      '#title' => $this->t('Offset'),
      '#description' => $this->t('Used for pagination, this it the number of records from a collection to skip. Default value is 0.'),
      '#default_value' => 0,
    ];

    $form['fields'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fields'),
      '#description' => $this->t('A comma-separated list of fields to return. Reference parameters of sub-objects with dot notation. If left empty, events.name, events.properties, and events.occurred_at will be returned.'),
      '#default_value' => 'events.name,events.properties,events.occurred_at',
    ];

    $form['exclude_fields'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Exclude fields'),
      '#description' => $this->t('A comma-separated list of fields to exclude. Reference parameters of sub-objects with dot notation. The options available match the options in "Fields".'),
      '#default_value' => '',
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
    $count = $form_state->getValue('count');
    $offset = $form_state->getValue('offset');
    $fields = explode(',', (string) $form_state->getValue('fields'));
    $exclude_fields = explode(',', (string) $form_state->getValue('exclude_fields'));

    $events = mailchimp_events_list_member_events($list, $email, $count, $offset, $fields, $exclude_fields);
    $form_state->set('events', $events);
    $form_state->setRebuild();
    $debug = $this->t("Called function: mailchimp_events_list_member_events(%list, %email, %count, %offset, %fields, %exclude_fields).", [
      '%list' => $list,
      '%email' => $email,
      '%count' => $count,
      '%offset' => $offset,
      '%fields' => print_r($fields, TRUE),
      '%exclude_fields' => print_r($exclude_fields, TRUE),
    ]);

    if ($events !== FALSE) {
      $this->messenger()->addStatus($debug);
    }
    else {
      $this->messenger()->addError($debug);
      $this->messenger()->addError($this->t('No results returned. Check the <a href=":watchdog">logs for Mailchimp</a>', [':watchdog' => Url::fromRoute('dblog.overview')->toString()]));
    }
  }

}
