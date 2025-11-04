<?php

namespace Drupal\mailchimp_campaign\Form;

use Drupal\Component\Utility\Html;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for the MailchimpCampaign entity edit form.
 *
 * @ingroup mailchimp_campaign
 */
class MailchimpCampaignForm extends ContentEntityForm {

  /**
   * The Mailchimp API service.
   *
   * @var \Drupal\mailchimp\ApiService
   */
  protected $apiService;

  /**
   * Configuration object for this builder.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * A cache backend interface.
   *
   * @var \Drupal\Core\Cache\CacheBackendInterface
   */
  protected $cache;

  /**
   * Render service.
   *
   * @var \Drupal\Core\Render\RendererInterface
   */
  protected $render;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->apiService = $container->get('mailchimp.api');
    $instance->config = $container->get('config.factory');
    $instance->messenger = $container->get('messenger');
    $instance->entityTypeManager = $container->get('entity_type.manager');
    $instance->entityDisplayRepository = $container->get('entity_display.repository');
    $instance->cache = $container->get('cache.mailchimp');
    $instance->render = $container->get('renderer');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $site_config = $this->config->get('system.site');

    // Attach campaign JS and CSS.
    $form['#attached']['library'][] = 'mailchimp_campaign/campaign-form';

    /** @var \Drupal\mailchimp_campaign\Entity\MailchimpCampaign $campaign */
    $campaign = $this->getEntity();

    $form['title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Title'),
      '#description' => $this->t('An internal name to use for this campaign. By default, the campaign subject will be used.'),
      '#required' => FALSE,
      '#default_value' => (!empty($campaign->mc_data->settings->title)) ? $campaign->mc_data->settings->title : '',
    ];
    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Subject'),
      '#required' => TRUE,
      '#default_value' => (!empty($campaign->mc_data->settings->subject_line)) ? $campaign->mc_data->settings->subject_line : '',
    ];
    $form['preview_text'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Preview text'),
      '#description' => $this->t('Text that shows as preview in e-mail clients, but not in the mail itself.'),
      '#required' => FALSE,
      '#default_value' => (!empty($campaign->mc_data->settings->preview_text)) ? $campaign->mc_data->settings->preview_text : '',
    ];
    $mailchimp_lists = $this->apiService->getAudiences();
    $form['list_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Audience'),
      '#description' => $this->t('Select the audience this campaign should be sent to.'),
      '#options' => $this->buildOptionList($mailchimp_lists),
      '#default_value' => (!empty($campaign->mc_data)) ? $campaign->mc_data->recipients->list_id : -1,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => 'Drupal\mailchimp_campaign\Form\MailchimpCampaignForm::listSegmentCallback',
      ],
    ];

    if (!empty($form_state->getValue('list_id'))) {
      $list_id = $form_state->getValue('list_id');
    }
    elseif ($campaign && $campaign->list) {
      $list_id = $campaign->list->id;
      if (isset($campaign->mc_data->recipients->segment_opts->saved_segment_id)) {
        $segment_id = $campaign->mc_data->recipients->segment_opts->saved_segment_id;
      }
    }

    $list_segments = [];
    if (isset($list_id)) {
      $list_segments = mailchimp_campaign_get_list_segments($list_id, NULL);
    }

    $form['list_segment_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Audience Tags'),
      '#description' => $this->t('Select the audience tags this campaign should be sent to.'),
    ];
    if (!empty($list_segments)) {
      $form['list_segment_id']['#options'] = $this->buildOptionList($list_segments, '-- Entire Audience --');
      $form['list_segment_id']['#default_value'] = (isset($segment_id)) ? $segment_id : '';
    }

    $form['list_segment_id']['#prefix'] = '<div id="list-segments-wrapper">';
    $form['list_segment_id']['#suffix'] = '</div>';

    $form['from_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('From Email'),
      '#description' => $this->t('the From: email address for your campaign message.'),
      '#default_value' => (!empty($campaign->mc_data)) ? $campaign->mc_data->settings->reply_to : $site_config->get('mail'),
      '#size' => 40,
      '#maxlength' => 255,
      '#required' => TRUE,
    ];
    $form['from_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('From Name'),
      '#description' => $this->t('the From: name for your campaign message (not an email address)'),
      '#default_value' => (!empty($campaign->mc_data)) ? $campaign->mc_data->settings->from_name : $site_config->get('name'),
      '#size' => 40,
      '#maxlength' => 255,
      '#required' => TRUE,
    ];
    $template_type_labels = [
      'user' => 'My Custom Templates',
      'base' => 'Basic Templates',
      'gallery' => 'Themes',
    ];

    $is_new = empty($campaign->getMcCampaignId());
    $no_selection_label = NULL;

    if ($is_new || $form_state->getValue('template_id') === '') {
      $no_selection_label = 'No Template';
    }

    $form['template_id'] = [
      '#type' => 'select',
      '#title' => $this->t('Template'),
      '#description' => '<p>' . $this->t('Select an email template to use.') . '</p><p>' . $this->t('Only a new campaign can be created without a template. A template must use custom HTML, have editable content areas, and not repeat.') . '</p>',
      '#options' => $this->buildOptionList(mailchimp_campaign_list_templates(), $no_selection_label, $template_type_labels),
      '#default_value' => (!empty($campaign->mc_data)) ? $campaign->mc_data->settings->template_id : -1,
      '#ajax' => [
        'callback' => 'Drupal\mailchimp_campaign\Form\MailchimpCampaignForm::templateCallback',
      ],
    ];

    $form['template_refresh'] = [
      '#value' => $this->t('Refresh current template and template list from Mailchimp'),
      '#type' => 'submit',
      '#submit' => ['::submitForm', '::refreshTemplates'],
      '#attributes' => [
        'class' => [
          'button',
          'button-action',
          'button--small',
        ],
      ],
    ];

    $form['content'] = [
      '#id' => 'content-sections',
      '#type' => 'fieldset',
      '#title' => $this->t('Content sections'),
      '#description' => $this->t('The editable content areas from your template or a generic HTML container if no template is selected.'),
      '#tree' => TRUE,
    ];

    $selected_template = $form_state->getValue('template_id');
    $mc_template = NULL;

    if (empty($selected_template)) {
      if ($selected_template !== '') {
        $mc_template = $campaign->mc_template;
      }
    }
    else {
      $mc_template = mailchimp_campaign_get_template($selected_template);
    }

    if (isset($list_id)) {
      $merge_vars_list = $this->apiService->getMergevars([$list_id]);
      $merge_vars = $merge_vars_list[$list_id];
    }
    else {
      $merge_vars = [];
    }

    $campaign_template = $campaign->getTemplate();
    $campaign_content = $form_state->getValue('content');

    $entity_type = NULL;

    if ($mc_template) {
      $template_name = $mc_template->name;

      if ($mc_template->drag_and_drop) {
        $form['content'] = [
          '#id' => 'content-sections',
          '#type' => 'fieldset',
          '#title' => $this->t("The ':template_name' template is drag and drop", [
            ':template_name' => $template_name ,
          ]),
          '#description' => $this->t('Only Custom HTML Templates can provide editable content areas.<p><strong>Related Mailchimp documentation:</strong><br><a href=":custom_link" target="_blank">Custom HTML Templates</a><br><a href=":editable_link" target="_blank">Editable Content Areas</a></p>', [
            ':custom_link' => 'https://mailchimp.com/help/import-a-custom-html-template/',
            ':editable_link' => 'https://mailchimp.com/help/create-editable-content-areas-with-mailchimps-template-language/',
          ]),
          '#tree' => TRUE,
        ];
      }
      elseif (empty((array) $mc_template->info->sections)) {
        $form['content'] = [
          '#id' => 'content-sections',
          '#type' => 'fieldset',
          '#title' => $this->t("The ':template_name' template has no editable content areas", [
            ':template_name' => $template_name ,
          ]),
          '#description' => $this->t('If you add one through Mailchimp, use the Refresh button above. <p><a href=":link" target="_blank">Mailchimp documentation on Editable Content Areas</a>.</p>', [
            ':link' => 'https://mailchimp.com/help/create-editable-content-areas-with-mailchimps-template-language/',
          ]),
          '#tree' => TRUE,
        ];
      }

      else {
        foreach ($mc_template->info->sections as $section => $content) {
          if (substr($section, 0, 6) == 'repeat') {
            $this->messenger->addWarning($this->t('WARNING: This template has repeating sections, which are not supported. You may want to select a different template.'));
          }
        }
        foreach ($mc_template->info->sections as $section => $content) {
          // Set the default value and text format to either saved campaign
          // values or defaults coming from the Mailchimp template.
          $default_value = $content;
          $format = 'mailchimp_campaign';

          if (($campaign_template != NULL) && isset($campaign_template[$section])) {
            $default_value = $campaign_template[$section]['value'];
            $format = $campaign_template[$section]['format'];
          }
          $form['content'][$section . '_wrapper'] = [
            '#type' => 'details',
            '#title' => Html::escape(ucfirst($section)),
            '#open' => FALSE,
          ];
          $form['content'][$section . '_wrapper'][$section] = [
            '#type' => 'text_format',
            '#format' => $format,
            '#title' => Html::escape(ucfirst($section)),
            '#default_value' => $default_value,
          ];

          if (isset($campaign_content[$section . '_wrapper']['entity_import']['entity_type'])) {
            $entity_type = $campaign_content[$section . '_wrapper']['entity_import']['entity_type'];
          }

          $form['content'][$section . '_wrapper'] += $this->getEntityImportFormElements($entity_type, $section);

          if (!empty($list_id)) {
            $form['content'][$section . '_wrapper'] += $this->getMergeVarsFormElements($merge_vars, $mailchimp_lists[$list_id]->name);
          }
        }
      }
    }
    else {
      $section = 'html';

      $form['content']['html_wrapper'] = [
        '#type' => 'details',
        '#title' => $this->t('Content'),
        '#open' => FALSE,
      ];
      $form['content']['html_wrapper']['html'] = [
        '#type' => 'text_format',
        '#format' => ($campaign_template != NULL) ? $campaign_template['html']['format'] : 'mailchimp_campaign',
        '#title' => $this->t('Content'),
        '#description' => $this->t('The HTML content of the campaign.'),
        '#access' => empty($form_state->getValue('template_id')),
        '#default_value' => ($campaign_template != NULL) ? $campaign_template['html']['value'] : '',
      ];

      if (isset($campaign_content[$section . '_wrapper']['entity_import']['entity_type'])) {
        $entity_type = $campaign_content[$section . '_wrapper']['entity_import']['entity_type'];
      }

      $form['content'][$section . '_wrapper'] += $this->getEntityImportFormElements($entity_type, $section);

      $list_name = (!empty($list_id)) ? $mailchimp_lists[$list_id]->name : '';
      $form['content'][$section . '_wrapper'] += $this->getMergeVarsFormElements($merge_vars, $list_name);
    }

    // Message preview:
    if (!empty($form_state->getValue('mailchimp_campaign_campaign_preview'))) {
      $form['preview_wrapper'] = [
        '#title' => $this->t('Campaign content preview'),
        '#type' => 'details',
        '#open' => TRUE,
      ];
      $form['preview_wrapper']['preview'] = [
        '#markup' => $form_state->getValue('mailchimp_campaign_campaign_preview'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);

    $actions['submit']['#value'] = $this->t('Save as draft');

    $actions['preview'] = [
      '#type' => 'submit',
      '#value' => $this->t('Preview content'),
      '#submit' => ['::submitForm', '::preview'],
    ];

    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $template_content = $this->parseTemplateContent($form_state->getValue('content') ?: []);

    if (empty($template_content)) {
      $form_state->setErrorByName('template_id', $this->t('The chosen template has no editable content areas'));
    }

    if ($form_state->getValue('op') == $form['actions']['submit']['#value']) {
      // Try to save form to campaign in MailChimp.
      $values = $form_state->getValues();

      $recipients = (object) [
        'list_id' => $values['list_id'],
      ];

      if (isset($values['list_segment_id']) && !empty($values['list_segment_id'])) {
        $recipients->segment_opts = (object) [
          'saved_segment_id' => (int) $values['list_segment_id'],
        ];
      }
      $settings = (object) [
        'subject_line' => $values['subject'],
        'title' => $values['title'],
        'from_name' => $values['from_name'],
        'reply_to' => $values['from_email'],
        'preview_text' => $values['preview_text'],
      ];

      /** @var \Drupal\mailchimp_campaign\Entity\MailchimpCampaign $campaign */
      $campaign = $this->getEntity();
      $campaign_id = mailchimp_campaign_save_campaign($template_content, $recipients, $settings, $values['template_id'], $campaign->getMcCampaignId());

      // In case of an error by MailChimp, the campaign_id is not set.
      // E.g. when the email address entered is not considered valid.
      // This would result in a SQL exception that leaves the user puzzled.
      // It's better to inform the user and abort the save operation.
      if (empty($campaign_id)) {
        $form_state->setErrorByName('submit', $this->t('An error occurred while saving this campaign to MailChimp service.'));
      }
      else {
        // Save campaign id to entity.
        $campaign->setMcCampaignId($campaign_id);
        $campaign->setTemplate($template_content);
      }
      return $campaign;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\mailchimp_campaign\Entity\MailchimpCampaign $campaign */
    $campaign = $this->getEntity();
    $result = $campaign->save();

    // Clear campaigns cache.
    $cache = $this->cache;
    $cache->deleteAll();

    $form_state->setRedirect('mailchimp_campaign.overview');
    return $result;
  }

  /**
   * Generates a preview of the campaign template content.
   */
  public function preview(array $form, FormStateInterface $form_state) {
    $raw_template_content = $form_state->getValue('content');
    if (empty($raw_template_content)) {
      $this->messenger->addWarning($this->t('The chosen template has no editable content areas to preview. Update the template from Mailchimp or try a different template.'));
    }
    else {
      $text = '';
      $template_content = $this->parseTemplateContent($raw_template_content);
      $content = mailchimp_campaign_render_template($template_content);
      foreach ($content as $key => $section) {
        $text .= "<h3>$key</h3>" . $section;
      }
      $form_state->setValue('mailchimp_campaign_campaign_preview', $text);
    }
    $form_state->setRebuild(TRUE);
  }

  /**
   * Ajax callback to render audience segments when an audience is selected.
   *
   * @param array $form
   *   Form API array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state information.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response with the rendered audience segment element.
   */
  public static function listSegmentCallback(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $list_segment_html = \Drupal::service('renderer')->render($form['list_segment_id']);
    $response->addCommand(new ReplaceCommand('#list-segments-wrapper', $list_segment_html));

    if (isset($form['content']['html_wrapper']['merge_vars'])) {
      $merge_vars_html = \Drupal::service('renderer')->render($form['content']['html_wrapper']['merge_vars']);
      $response->addCommand(new ReplaceCommand('.merge-vars-wrapper', $merge_vars_html));
    }

    return $response;
  }

  /**
   * Ajax callback to render content with a template is selected.
   *
   * @param array $form
   *   Form API array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state information.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response with the rendered content element.
   */
  public static function templateCallback(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $response->addCommand(new ReplaceCommand('#content-sections', $form['content']));

    return $response;
  }

  /**
   * Ajax callback to render content with a template is selected.
   *
   * @param array $form
   *   Form API array structure.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state information.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response with the rendered content element.
   */
  public static function entityTypeCallback(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $triggering_element = $form_state->getTriggeringElement();

    $content_wrapper = $triggering_element['#parents'][1];
    $entity_import_wrapper = $triggering_element['#ajax']['wrapper'];

    $html = '<div id="' . $entity_import_wrapper . '" class="content-entity-lookup-wrapper">';
    $html .= \Drupal::service('renderer')->render($form['content'][$content_wrapper]['entity_import']['entity_id']);
    $html .= \Drupal::service('renderer')->render($form['content'][$content_wrapper]['entity_import']['entity_view_mode']);
    $html .= '</div>';

    $response->addCommand(new ReplaceCommand('#' . $entity_import_wrapper, $html));

    return $response;
  }

  /**
   * Fetches list of templates from Mailchimp.
   *
   * And replaces cacheDefaults to most recent 30 templates.
   */
  public function refreshTemplates(array $form, FormStateInterface $form_state) {
    mailchimp_campaign_get_template($form_state->getValue('template_id'), TRUE);
    $form_state->setRebuild(TRUE);
  }

  /**
   * Returns an option list for a given array of items.
   *
   * @param array $list
   *   Array of item data containing 'id' and 'name' properties.
   * @param string $no_selection_label
   *   The option value to display when no option is selected.
   * @param array $labels
   *   Optional associative array of list indexes to custom labels.
   *
   * @return array
   *   Associative array of item IDs to name.
   */
  private function buildOptionList(array $list, $no_selection_label = '-- Select --', array $labels = []) {
    $options = [];
    if ($no_selection_label) {
      $options[''] = $no_selection_label;
    }
    foreach ($list as $index => $item) {
      if (!isset($item->id)) {
        $label = $labels[$index] ?? $index;
        if (count($item)) {
          $options[$label] = $this->buildOptionList($item, FALSE, $labels);
        }
      }
      else {
        $options[$item->id] = $item->name;
      }
    }

    return $options;
  }

  /**
   * Returns an option list of entities based on data from entity_get_info().
   *
   * Filters out entities that do not contain a title field, as they cannot
   * be used to import content into templates.
   *
   * @param array $entity_info
   *   Array of entities as returned by entity_get_info().
   *
   * @return array
   *   Associative array of entity IDs to name.
   */
  private function buildEntityOptionList(array $entity_info) {
    $options = [
      '' => '-- Select --',
    ];

    foreach ($entity_info as $entity_id => $entity_data) {
      // Exclude Mailchimp entities.
      if (strpos($entity_id, 'mailchimp') === FALSE) {
        $options[$entity_id] = $entity_data->getLabel();
      }
    }

    return $options;
  }

  /**
   * Returns an option list of entity view modes.
   *
   * @param string $entity_type
   *   Entity type to build view mode options for.
   *
   * @return array
   *   Associative array of view mode IDs to name.
   */
  private function buildEntityViewModeOptionList($entity_type) {
    $options = [];

    $view_modes = $this->entityDisplayRepository->getViewModes($entity_type);

    foreach ($view_modes as $view_mode_id => $view_mode_data) {
      $options[$view_mode_id] = $view_mode_data['label'];
    }

    return $options;
  }

  /**
   * Gets form elements used in the entity import feature.
   *
   * @param string $entity_type
   *   The type of entity to import.
   * @param string $section
   *   The content section these fields are displayed in.
   *
   * @return array
   *   Array of form elements used to display entity imports.
   */
  private function getEntityImportFormElements($entity_type, $section) {
    $form = [];

    // Get available entity types.
    $entity_info = $this->getEntitiesForContentImport();
    $entity_options = $this->buildEntityOptionList($entity_info);

    $form['entity_import'] = [
      '#id' => 'entity-import',
      '#type' => 'details',
      '#title' => $this->t('Insert site content'),
      '#description' => $this->t('<b>For use only with text filters that use the Mailchimp Campaign filter</b><br />You can insert an entity of a given type and pick the view mode that will be rendered within this campaign section.'),
      '#open' => FALSE,
    ];

    $form['entity_import']['entity_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Entity Type'),
      '#options' => $entity_options,
      '#default_value' => $entity_type,
      '#ajax' => [
        'callback' => 'Drupal\mailchimp_campaign\Form\MailchimpCampaignForm::entityTypeCallback',
        'wrapper' => $section . '-content-entity-lookup-wrapper',
      ],
    ];
    $form['entity_import']['entity_type']['#attributes']['class'][] = $section . '-entity-import-entity-type';

    $form['entity_import']['entity_import_wrapper'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => $section . '-content-entity-lookup-wrapper',
      ],
    ];

    if ($entity_type != NULL) {
      // Get available entity view modes.
      $entity_view_mode_options = $this->buildEntityViewModeOptionList($entity_type);

      $form['entity_import']['entity_id'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Entity Title'),
        '#maxlength' => 255,
        // Pass entity type as first parameter to autocomplete callback.
        '#autocomplete_route_name' => 'mailchimp_campaign.entity_autocomplete',
        '#autocomplete_route_parameters' => [
          'entity_type' => $entity_type,
        ],
      ];
      $form['entity_import']['entity_id']['#attributes']['id'] = $section . '-entity-import-entity-id';

      $form['entity_import']['entity_view_mode'] = [
        '#type' => 'select',
        '#title' => $this->t('View Mode'),
        '#options' => $entity_view_mode_options,
        '#attributes' => [
          'id' => $section . '-entity-import-entity-view-mode',
        ],
      ];
    }

    $form['entity_import']['entity_import_link'] = [
      '#type' => 'item',
      '#markup' => '<a id="' . $section . '-add-entity-token-link" class="add-entity-token-link" href="javascript:void(0);">' . $this->t('Insert entity token') . '</a>',
      '#states' => [
        'invisible' => [
          ':input[name="content[' . $section . '_wrapper][entity_import][entity_type]"]' => ['value' => ''],
        ],
      ],
    ];

    $form['entity_import']['entity_import_tag'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => $section . '-entity-import-tag-field',
      ],
      '#states' => [
        'invisible' => [
          ':input[name="content[' . $section . '_wrapper][entity_import][entity_type]"]' => ['value' => ''],
        ],
      ],
    ];

    return $form;
  }

  /**
   * Returns an array of entities based on data from entity_get_info().
   *
   * Filters out entities that do not contain a title field, as they cannot
   * be used to import content into templates.
   *
   * @return array
   *   Filtered entities from entity_get_info().
   */
  private function getEntitiesForContentImport() {
    $entity_info = $this->entityTypeManager->getDefinitions();

    $filtered_entities = [];

    foreach ($entity_info as $key => $entity) {
      $entity_keys = $entity->getKeys();
      foreach ($entity_keys as $entity_key => $value) {
        if ($value == 'title') {
          $filtered_entities[$key] = $entity;
          continue;
        }
      }
    }

    return $filtered_entities;
  }

  /**
   * Gets form elements used in the merge vars feature.
   *
   * @param array $merge_vars
   *   Array of Mailchimp merge vars for the current audience.
   * @param string $list_name
   *   The name of the current audience.
   *
   * @return array
   *   Array of form elements used to display merge vars.
   *
   * @see \Drupal\mailchimp\ApiService::getMergevars
   */
  private function getMergeVarsFormElements(array $merge_vars, $list_name) {
    $form = [];

    $form['merge_vars'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'merge-vars-wrapper',
        ],
      ],
    ];

    $merge_vars_url = Url::fromUri('https://admin.mailchimp.com/lists/', ['attributes' => ['target' => '_blank']]);

    $form['merge_vars']['content'] = [
      '#type' => 'item',
      '#title' => 'Mailchimp merge variables',
      '#markup' => $this->buildMergeVarsHtml($merge_vars),
      '#description' => $this->t(
        'Insert merge variables from the %list_name audience or one of the @standard_link.',
        [
          '%list_name' => $list_name,
          '@standard_link' => Link::fromTextAndUrl($this->t('standard Mailchimp merge variables'), $merge_vars_url)->toString(),
        ]
      ),
    ];

    return $form;
  }

  /**
   * Builds a HTML string used to render merge vars on the campaign form.
   *
   * @param array $merge_vars
   *   Array of merge vars. @see mailchimp_lists_get_merge_vars.
   *
   * @return string
   *   HTML string containing formatted merge vars.
   */
  private function buildMergeVarsHtml(array $merge_vars) {
    if (!empty($merge_vars)) {
      $element = [];

      $element['mergevars_table'] = [
        '#type' => 'table',
        '#empty' => '',
      ];

      foreach ($merge_vars as $var) {
        $key = (string) $var->name;
        $element['mergevars_table'][$key] = [
          '#markup' => $var->name,
        ];

        if (isset($var->link) && !is_null($var->link)) {
          $element['mergevars_table'][$var->link] = [
            '#markup' => '<a id="merge-var-' . $var->tag . '" class="add-merge-var" href="javascript:void(0);">*|' . $var->tag . '|*</a>',
          ];
        }
      }

      return $this->render->render($element);
    }
    else {
      return $this->t('No custom merge vars exist for the current audience.');
    }
  }

  /**
   * Parses template content to remove wrapper elements from tree.
   *
   * @param array $content
   *   The template content array.
   *
   * @return array
   *   The template content array minus wrapper elements.
   */
  private function parseTemplateContent(array $content) {
    $template_content = [];
    $content_keys = array_keys($content);
    foreach ($content_keys as $content_key) {
      if (strpos($content_key, '_wrapper') !== FALSE) {
        // If this element is a wrapper, add the element contained
        // within the wrapper to the template content.
        $new_content_key = str_replace('_wrapper', '', $content_key);
        $template_content[$new_content_key] = $content[$content_key][$new_content_key];
      }
      else {
        // If this element is not a wrapper, add it to the template content.
        $template_content[$content_key] = $content[$content_key];
      }
    }
    return $template_content;
  }

}
