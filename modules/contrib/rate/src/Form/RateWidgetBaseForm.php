<?php

namespace Drupal\rate\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\Html;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\votingapi\Entity\Vote;
use Drupal\votingapi\VoteResultFunctionManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for rate vote forms.
 */
class RateWidgetBaseForm extends ContentEntityForm {

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * The votingapi result manager.
   *
   * @var \Drupal\votingapi\VoteResultFunctionManager
   */
  protected $votingapiResult;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The logger instance.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The config factory wrapper to fetch settings.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $config;

  /**
   * Constructs a RateWidgetBaseForm object.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   * @param \Drupal\votingapi\VoteResultFunctionManager $votingapi_result
   *   Vote result function service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   Entity type manager service.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account service.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(EntityRepositoryInterface $entity_repository, EntityTypeBundleInfoInterface $entity_type_bundle_info, TimeInterface $time, VoteResultFunctionManager $votingapi_result, EntityTypeManagerInterface $entity_type_manager, AccountInterface $account, LoggerInterface $logger, ConfigFactoryInterface $config_factory) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->time = $time;
    $this->votingapiResult = $votingapi_result;
    $this->entityTypeManager = $entity_type_manager;
    $this->account = $account;
    $this->logger = $logger;
    $this->config = $config_factory->get('rate.settings');
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time'),
      $container->get('plugin.manager.votingapi.resultfunction'),
      $container->get('entity_type.manager'),
      $container->get('current_user'),
      $container->get('logger.factory')->get('rate'),
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    $entity = $this->getEntity();
    $voted_entity_type = $entity->getVotedEntityType();
    $voted_entity_id = $entity->getVotedEntityId();
    $voted_entity = $this->entityTypeManager->getStorage($voted_entity_type)->load($voted_entity_id);

    $additional_form_id_parts = [];
    $additional_form_id_parts[] = $voted_entity->getEntityTypeId();
    $additional_form_id_parts[] = $voted_entity->bundle();
    $additional_form_id_parts[] = $voted_entity->id();
    $additional_form_id_parts[] = $entity->bundle();
    $additional_form_id_parts[] = $entity->rate_widget->value;
    $additional_form_id_parts[] = $entity->get('user_id')->target_id;
    $form_id = implode('_', $additional_form_id_parts);

    return $form_id;
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $entity = $this->getEntity();
    $voted_entity_type = $entity->getVotedEntityType();
    $voted_entity_id = $entity->getVotedEntityId();
    $voted_entity = $this->entityTypeManager->getStorage($voted_entity_type)->load($voted_entity_id);
    $result_function = $this->getResultFunction($form_state);
    $options = $form_state->get('options');
    $option_classes = $form_state->get('classes');
    $form_id = Html::getUniqueId('rate-widget-base-form');
    $plugin = $form_state->get('plugin');
    $settings = $form_state->get('settings');
    $voting = $settings->get('voting');
    $display = $settings->get('display');
    $results = $settings->get('results');
    $template = $settings->get('template');
    $rate_widget = $form_state->get('rate_widget');
    $value_type = $entity->get('value_type')->value;
    $is_views_field = $form_state->get('is_views_field');
    $views_url = $form_state->get('views_url');

    $form['#cache']['contexts'][] = 'user.permissions';
    $form['#cache']['contexts'][] = 'user.roles:authenticated';

    $form['#attributes']['id'] = $form_id;

    $rate_options = [];

    // Remove labels on all templates, except for options and labelled widgets.
    $labelled_widgets = ['yesno', 'custom', 'emotion'];
    if ($value_type != 'option') {
      if (!in_array($template, $labelled_widgets)) {
        // Remove the labels.
        foreach ($options as $key => $value) {
          $rate_options[$key] = '';
        }
      }
      else {
        $rate_options = $options;
      }
    }
    else {
      // Options with labels.
      $rate_options = $options;
    }

    $form['value'] = [
      '#prefix' => '<div class="' . $template . '-rating-wrapper">',
      '#suffix' => '</div>',
      '#type' => 'radios',
      '#options' => $rate_options,
      '#default_value' => $entity->isNew() ? NULL : (int) $entity->getValue(),
      '#attributes' => ['class' => [$template . '-rating-input']],
      '#theme_wrappers' => [],
      '#wrapped_label' => TRUE,
    ];

    $vote_type = $entity->bundle();
    $user_id = $entity->get('user_id')->target_id;

    // Define which aggregation to use on the database query.
    if ($value_type == 'points') {
      $aggregation = 'SUM';
    }
    elseif ($value_type == 'percent') {
      $aggregation = 'AVG';
    }
    else {
      $aggregation = 'COUNT';
    }

    // Get the results for showing them in options.
    if (isset($results['result_type'])) {
      if ($results['result_type'] == 'user_vote_empty' || $results['result_type'] == 'user_vote_average') {
        // Get the user votes first.
        $option_values = $plugin->getVotes($form_state->get('entity_type'), $form_state->get('entity_bundle'), $form_state->get('entity_id'), $vote_type, $value_type, $rate_widget, $user_id, $aggregation);
        if ($results['result_type'] == 'user_vote_average' && count($option_values) == 0) {
          // Check if the options have the function field set.
          $full_options = $settings->get('options');
          foreach ($full_options as $option_id => $full_option) {
            if (!empty($full_option['function'])) {
              $function = $full_option['function'] . ':' . $voted_entity_type . '.' . $voted_entity->bundle() . '.' . $rate_widget;
              $option_values[$full_option['value']] = $this->getResults($function);
            }
            else {
              $option_values = $plugin->getVotes($form_state->get('entity_type'), $form_state->get('entity_bundle'), $form_state->get('entity_id'), $vote_type, $value_type, $rate_widget, NULL, $aggregation);
            }
          }
        }
      }
      else {
        $option_values = $plugin->getVotes($form_state->get('entity_type'), $form_state->get('entity_bundle'), $form_state->get('entity_id'), $vote_type, $value_type, $rate_widget, NULL, $aggregation);
      }
    }

    // Save the results for each option separately and add classes.
    if (isset($template) && $template != 'fivestar') {
      $form['value']['#attributes']['class'][] = 'rating-input';

      foreach ($options as $key => $option) {
        $form['value'][$key]['#attributes']['twig-suggestion'] = 'rating-input';
        $form['value'][$key]['#attributes']['class'][] = 'rating-input';
        $form['value'][$key]['#attributes']['class'][] = $template . '-rating-input';

        // Set the option results.
        $form['value'][$key]['#option_result'] = isset($option_values[$key]) ? round($option_values[$key], 2) : 0;

        if (isset($option_classes[$key]) && ($option_classes[$key] != NULL)) {
          $form['value'][$key]['#label_attributes']['class'][] = 'rating-label';
          $form['value'][$key]['#label_attributes']['class'][] = 'rating-label-' . $template;
          $form['value'][$key]['#label_attributes']['class'][] = 'rating-label-' . $template . '-' . strtolower(Html::cleanCssIdentifier($option));
          $form['value'][$key]['#label_attributes']['class'][] = $option_classes[$key];
        }
        else {
          $form['value'][$key]['#label_attributes']['class'][] = 'rating-label';
          $form['value'][$key]['#label_attributes']['class'][] = $template . '-rating-label';
          $form['value'][$key]['#label_attributes']['class'][] = $template . '-rating-label-' . strtolower(Html::cleanCssIdentifier($option));
        }
      }
    }
    // Handle fivestar classes and option results.
    else {
      foreach ($options as $key => $option) {
        // Add attributes and classes to the inputs.
        $form['value'][$key]['#attributes']['twig-suggestion'] = 'rating-input';
        $form['value'][$key]['#attributes']['class'][] = 'rating-input';
        $form['value'][$key]['#attributes']['class'][] = $template . '-rating-input';
        $form['value'][$key]['#attributes']['class'][] = $template . '-rating-input-' . $key;

        // Add attributes and classes to the labels.
        $form['value'][$key]['#label_attributes']['class'][] = 'rating-label';
        $form['value'][$key]['#label_attributes']['class'][] = $template . '-rating-label';
        $form['value'][$key]['#label_attributes']['class'][] = $template . '-rating-label-' . $key;

        if (isset($option_classes[$key]) && ($option_classes[$key] != NULL)) {
          $form['value'][$key]['#label_attributes']['class'][] = $option_classes[$key];
        }
      }
      // Show the result after the last option.
      $entity_value = $entity->getValue();
      if (isset($results['result_type']) && $results['result_type'] != 'vote_hidden') {
        if ($results['result_type'] == 'user_vote_empty') {
          $vote_avg = $entity_value ?? FALSE;
        }
        elseif ($results['result_type'] == 'user_vote_average') {
          $vote_avg = $entity_value ?? round($this->getResults($result_function), 1);
        }
        elseif ($results['result_type'] == 'vote_average') {
          $vote_avg = round($this->getResults($result_function), 1);
        }
        $form['value'][$key]['#option_result'] = $vote_avg;
        // Show the average value as stars.
        if ($entity->isNew()) {
          if ($results['result_type'] != 'user_vote_empty' && $vote_avg != FALSE) {
            foreach ($options as $option_id => $option) {
              if ($option_id <= $vote_avg) {
                $form['value'][$option_id]['#label_attributes']['class']['average'] = 'average';
              }
            }
          }
        }
      }
    }

    // Set 'rate-voted' html class to radio element.
    $form = $this->setVotedClass($form, $entity, $options);

    // Set the rate widget to readonly, if the entity uses a vote deadline.
    $deadline_disabled = $this->checkDeadlineDisabled($voted_entity, $voting);
    $form['value']['#deadline_disabled'] = ($deadline_disabled === TRUE) ? TRUE : FALSE;

    if ((isset($display['readonly']) && $display['readonly'] === 1) || !$plugin->canVote($entity) || $deadline_disabled === TRUE) {
      $form['value']['#disabled'] = TRUE;
      $form['value']['#prefix'] = '<div class="' . $template . '-rating-wrapper rate-disabled" can-edit="false">';
    }
    else {
      $form['value']['#disabled'] = FALSE;
      $form['value']['#prefix'] = '<div class="' . $template . '-rating-wrapper rate-enabled" can-edit="true">';
    }

    // Take care of the widget default value.
    if (!$entity->isNew()) {
      if (!isset($results['result_type']) || $results['result_type'] == '0') {
        $form['value']['#default_value'] = $this->getResults($result_function);
      }
      else {
        $form['value']['#default_value'] = (int) $entity->getValue();
      }
    }

    // Get the results container.
    $vote_summary = $plugin->getVoteSummary($entity);
    if (isset($results['result_position']) && $results['result_position'] !== 'hidden') {
      $form['result'] = [
        '#theme' => 'container',
        '#attributes' => [
          'class' => ['vote-result'],
        ],
        '#children' => [],
        '#weight' => 100,
      ];
      $form['result']['#children']['result'] = $vote_summary;
      $form['result']['#children']['result']['#disabled'] = $form['value']['#disabled'];
      $form['result']['#children']['result']['#deadline_disabled'] = $deadline_disabled;
    }

    // The form submit button.
    $form['submit'] = $form['actions']['submit'];
    $form['actions']['#access'] = FALSE;

    $form['submit'] += [
      '#type' => 'button',
      '#attributes' => [
        'class' => [$template . '-rating-submit'],
      ],
      '#ajax' => [
        'disable-refocus' => TRUE,
        'callback' => '::ajaxSubmit',
        'event' => 'click',
        'wrapper' => $form_id,
        'progress' => [
          'type' => NULL,
        ],
      ],
    ];

    // Set the url of the ajax call if the rate form is in a view.
    if ($is_views_field == TRUE && !is_null($views_url)) {
      $form['submit']['#ajax']['url'] = $views_url;
      $form['submit']['#ajax']['options'] = [
        'query' => $this->getRequest()->query->all() + [FormBuilderInterface::AJAX_FORM_REQUEST => TRUE],
      ];
    }

    // Base widget template. Can create additional twig templates.
    $form['#theme'] = 'rate_widget';
    $form['#rate_widget'] = $rate_widget;
    $form['#widget_template'] = $template;
    $form['#display_settings'] = $display;
    $form['#results_settings'] = $results;
    $form['#results'] = $vote_summary;
    $form['#results']['#disabled'] = $form['value']['#disabled'];
    $form['#results']['#deadline_disabled'] = $deadline_disabled;
    return $form;
  }

  /**
   * Get result function.
   */
  protected function getResultFunction(FormStateInterface $form_state) {
    $entity = $this->getEntity();
    return ($form_state->get('resultfunction')) ? $form_state->get('resultfunction') : 'rate_average:' . $entity->getVotedEntityType() . '.' . $form_state->get('entity_bundle') . '.' . $entity->rate_widget->value;
  }

  /**
   * Get results.
   */
  public function getResults($result_function = FALSE, $reset = FALSE) {
    $entity = $this->entity;
    if ($reset) {
      drupal_static_reset(__FUNCTION__);
    }
    $resultCache = &drupal_static(__FUNCTION__);

    if (!$resultCache || !isset($resultCache[$entity->getVotedEntityType()][$entity->getVotedEntityId()])) {
      $resultCache[$entity->getVotedEntityType()][$entity->getVotedEntityId()] = $this->votingapiResult->getResults($entity->getVotedEntityType(), $entity->getVotedEntityId());
    }

    $result = $resultCache[$entity->getVotedEntityType()][$entity->getVotedEntityId()] ?? [];
    $result = !empty($result) && array_key_exists($entity->bundle(), $result) ? $result[$entity->bundle()] : [];

    if ($result_function && array_key_exists($result_function, $result) && $result[$result_function]) {
      $result = $result[$result_function];
    }
    else {
      $result = 0;
    }
    return $result;
  }

  /**
   * Ajax submit handler.
   */
  public function ajaxSubmit(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $settings = $form_state->get('settings');
    $options = $form_state->get('options');
    $voting = $settings->get('voting');
    $display = $settings->get('display');
    $results = $settings->get('results');
    $plugin = $form_state->get('plugin');
    $result_function = $this->getResultFunction($form_state);
    $template = $settings->get('template');
    $rate_widget = $form_state->get('rate_widget');
    $voted_entity_id = $entity->getVotedEntityId();
    $voted_entity_type = $entity->getVotedEntityType();
    $user_input = $form_state->getUserInput()['value'];
    $value_type = $entity->get('value_type')->value;
    $user_id = $entity->get('user_id')->target_id;
    $disable_log = $this->config->get('disable_log');
    $vote_type = $entity->bundle();

    $voted_entity = $this->entityTypeManager->getStorage($voted_entity_type)->load($voted_entity_id);
    $deadline_disabled = $this->checkDeadlineDisabled($voted_entity, $voting);
    $display_readonly = (isset($display['readonly']) && $display['readonly'] === 1) ? TRUE : FALSE;

    if (!$plugin->canVote($entity) || $display_readonly === TRUE || $deadline_disabled === TRUE) {
      $form['value']['#disabled'] = TRUE;
      $form['value']['#prefix'] = '<div class="' . $template . '-rating-wrapper rate-disabled" can-edit="false">';
    }
    else {
      $form['value']['#disabled'] = FALSE;
      $form['value']['#prefix'] = '<div class="' . $template . '-rating-wrapper rate-enabled" can-edit="true">';
    }

    $this->save($form, $form_state, $display_readonly, $deadline_disabled);

    // The entity is saved now - reset the resultCache and get the value.
    $result_value = $this->getResults($result_function, TRUE);

    // If the user clicked the same option - delete last vote.
    if ($form_state->getUserInput()['value'] == $form['value']['#default_value']) {
      $entity->delete();
      if ($disable_log == FALSE) {
        $message = 'Vote ' . $user_input . ' on ' . $voted_entity_id . ' was cancelled. Vote ' . $entity->id() . ' was deleted.';
        $this->logger->notice($message);
      }
      $entity = $plugin->getEntityForVoting($form_state->get('entity_type'), $form_state->get('entity_bundle'), $form_state->get('entity_id'), $entity->bundle(), $value_type, $rate_widget, $settings, $user_id);
    }
    else {
      if ($disable_log == FALSE) {
        $message = 'Vote ' . $entity->id() . ' saved. Voted ' . $user_input . ' on ' . $voted_entity_id . '.';
        $this->logger->notice($message);
      }
    }

    if (isset($results['result_position']) && $results['result_position'] !== 'hidden') {
      $vote_summary = $plugin->getVoteSummary($entity);
      $form['result']['#children']['result'] = $vote_summary;
      $form['#results'] = $vote_summary;
      $form['#results']['#disabled'] = $form['value']['#disabled'];
      $form['#results']['#deadline_disabled'] = $form['value']['#deadline_disabled'];
    }

    // Define which aggregation to use on the database query.
    if ($value_type == 'points') {
      $aggregation = 'SUM';
    }
    elseif ($value_type == 'percent') {
      $aggregation = 'AVG';
    }
    else {
      $aggregation = 'COUNT';
    }

    // Get the results for showing them in options.
    if (isset($results['result_type'])) {
      if ($results['result_type'] == 'user_vote_empty' || $results['result_type'] == 'user_vote_average') {
        // Get the user votes first.
        $option_values = $plugin->getVotes($form_state->get('entity_type'), $form_state->get('entity_bundle'), $form_state->get('entity_id'), $vote_type, $value_type, $rate_widget, $user_id, $aggregation);
        if ($results['result_type'] == 'user_vote_average' && count($option_values) == 0) {
          // Check if the options have the function field set.
          $full_options = $settings->get('options');
          foreach ($full_options as $option_id => $full_option) {
            if (!empty($full_option['function'])) {
              $function = $full_option['function'] . ':' . $voted_entity_type . '.' . $voted_entity->bundle() . '.' . $rate_widget;
              $option_values[$full_option['value']] = $this->getResults($function);
            }
            else {
              $option_values = $plugin->getVotes($form_state->get('entity_type'), $form_state->get('entity_bundle'), $form_state->get('entity_id'), $vote_type, $value_type, $rate_widget, NULL, $aggregation);
            }
          }
        }
      }
      else {
        $option_values = $plugin->getVotes($form_state->get('entity_type'), $form_state->get('entity_bundle'), $form_state->get('entity_id'), $vote_type, $value_type, $rate_widget, NULL, $aggregation);
      }
    }

    // Handle the different templates/vote types.
    if (isset($template)) {
      if (isset($results['result_type']) && $results['result_type'] != 'vote_hidden') {
        // Add the result after the last option (fivestar).
        if ($template == 'fivestar') {
          $entity_value = $entity->getValue();
          if ($results['result_type'] == 'user_vote_empty') {
            $vote_avg = $entity_value ?? FALSE;
          }
          elseif ($results['result_type'] == 'user_vote_average') {
            $vote_avg = $entity_value ?? round($result_value, 1);
          }
          elseif ($results['result_type'] == 'vote_average') {
            $vote_avg = round($result_value, 1);
          }
          // Add the result to the last option.
          $key = array_key_last($options);
          $form['value'][$key]['#option_result'] = $vote_avg;

          if ($entity->isNew()) {
            if ($results['result_type'] != 'user_vote_empty' && $vote_avg != FALSE) {
              foreach ($options as $option_id => $option) {
                if ($option_id <= $vote_avg) {
                  $form['value'][$option_id]['#label_attributes']['class']['average'] = 'average';
                }
              }
            }
          }
          else {
            if ($results['result_type'] == 'user_vote_average' || $results['result_type'] == 'vote_average') {
              foreach ($options as $option_id => $option) {
                if (isset($form['value'][$option_id]['#label_attributes']['class']['average'])) {
                  unset($form['value'][$option_id]['#label_attributes']['class']['average']);
                }
              }
            }
          }
        }
        // For all other templates - add the result to each option.
        else {
          foreach ($options as $key => $option) {
            $form['value'][$key]['#option_result'] = isset($option_values[$key]) ? round($option_values[$key], 2) : 0;
          }
        }
      }
    }

    // Set 'rate-voted' html class to radio element.
    $form = $this->setVotedClass($form, $entity, $options);

    $form_state->setRebuild(TRUE);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state, $display_readonly = FALSE, $deadline_disabled = FALSE) {
    $entity = $this->getEntity();
    $plugin = $form_state->get('plugin');
    $is_bot_vote = $plugin->isBotVote();

    if ($plugin->canVote($entity) && !$is_bot_vote) {
      if ($display_readonly === FALSE || $deadline_disabled = FALSE) {
        $return = parent::save($form, $form_state);
        // @todo Could be simplified if https://www.drupal.org/project/votingapi/issues/3159592 was done.
        $voted_entity_id = $entity->getVotedEntityId();
        $voted_entity_type_id = $entity->getVotedEntityType();
        $voted_entity = $this->entityTypeManager->getStorage($voted_entity_type_id)->load($voted_entity_id);
        Cache::invalidateTags(['vote:' . $voted_entity->bundle() . ':' . $voted_entity_id]);
        return $return;
      }
    }
    return FALSE;
  }

  /**
   * Helper function to set 'rate-voted' html class to radio element.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\votingapi\Entity\Vote $vote_entity
   *   Vote entity.
   * @param array $radio_options
   *   Form radio options.
   *
   * @return array
   *   The form structure.
   */
  protected function setVotedClass(array $form, Vote $vote_entity, array $radio_options) {
    if (!$vote_entity->isNew()) {
      $vote_value = (int) $vote_entity->getValue();
      foreach ($radio_options as $key => $option) {
        if ($vote_value === $key) {
          $form['value'][$key]['#label_attributes']['class']['rate-voted'] = 'rate-voted';
        }
        else {
          unset($form['value'][$key]['#label_attributes']['class']['rate-voted']);
        }
      }
    }
    else {
      foreach ($radio_options as $key => $option) {
        unset($form['value'][$key]['#label_attributes']['class']['rate-voted']);
      }
    }
    return $form;
  }

  /**
   * Helper function to check the voting deadline.
   *
   * @param object $voted_entity
   *   The voted entity object.
   * @param array $voting
   *   The rate widget voting settings.
   *
   * @return bool
   *   True if the rate widget should be disabled as deadline has passed.
   */
  protected function checkDeadlineDisabled($voted_entity, array $voting) {
    $deadline_disabled = FALSE;
    if (isset($voting['use_deadline']) && $voting['use_deadline'] == 1) {
      // Get the rate_vote_deadline field.
      if ($voted_entity->hasField('field_rate_vote_deadline')) {
        $deadline = $voted_entity->get('field_rate_vote_deadline')->getString();
        $current_time = $this->time->getRequestTime();
        // Disable the widget if deadline to vote was set and was passed.
        if (!empty($deadline) && (strtotime($deadline) <= $current_time)) {
          $deadline_disabled = TRUE;
        }
      }
    }
    return $deadline_disabled;
  }

}
