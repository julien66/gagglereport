<?php

namespace Drupal\exclude_node_title\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\exclude_node_title\ExcludeNodeTitleManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Form object class for Exclude Node Title settings.
 */
class AdminSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Defines the interface for a configuration object factory.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typed_config_manager
   *   The typed config manager.
   * @param \Drupal\exclude_node_title\ExcludeNodeTitleManagerInterface $excludeNodeTitleManager
   *   The Exclude Node Title module settings manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundleInfo
   *   Discovery and retrieval of entity type bundles manager.
   * @param \Drupal\Core\Entity\EntityDisplayRepositoryInterface $entityDisplayRepository
   *   The entity display repository.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $moduleHandler
   *   The module handler service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typed_config_manager,
    protected ExcludeNodeTitleManagerInterface $excludeNodeTitleManager,
    protected EntityTypeBundleInfoInterface $bundleInfo,
    protected EntityDisplayRepositoryInterface $entityDisplayRepository,
    protected ModuleHandlerInterface $moduleHandler,
  ) {
    parent::__construct($config_factory, $typed_config_manager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('exclude_node_title.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('entity_display.repository'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'exclude_node_title_admin_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return [
      'exclude_node_title.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $enabled_link = Link::fromTextAndUrl($this->t('Search module'), Url::fromRoute('system.modules_list', [], ['fragment' => 'module-search']))->toString();
    $form['#attached']['library'][] = 'system/drupal.system';

    $form['exclude_node_title_search'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Remove node title from search pages'),
      '#description' => $this->t('You need to have @searchmodule enabled.', [
        '@searchmodule' => $enabled_link,
      ]),
      '#default_value' => $this->excludeNodeTitleManager->isSearchExcluded(),
      '#disabled' => !$this->moduleHandler->moduleExists('search'),
    ];

    $form['render_type'] = [
      '#type' => 'radios',
      '#title' => $this->t('Type of rendering'),
      '#options' => [
        'remove' => $this->t('Remove text'),
        'hidden' => $this->t('Hidden class'),
      ],
      '#description' => $this->t('Remove text will remove all text within the title. This may leave the HTML tag. Hidden class will add a <code>.hidden</code> class to the HTML tag where appropriate.'),
      '#default_value' => $this->excludeNodeTitleManager->getRenderType(),
    ];

    $form['content_type'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Exclude title by content types'),
      '#description' => $this->t('<strong>All nodes.</strong> excludes the Node title from all the node displays using the View Mode(s) you select.<br /><strong>User defined nodes.</strong> does not, by default, hide any Node title. However, it provides users with the permission to exclude node title a checkbox on the node edit form that allows them to exclude node titles, from the View Modes selected in this form, on a node-by-node basis.'),
      '#collapsible' => TRUE,
      '#collapsed' => FALSE,
      '#tree' => TRUE,
    ];

    foreach ($this->bundleInfo->getBundleInfo('node') as $node_type => $node_type_info) {
      $form['#attached']['drupalSettings']['exclude_node_title']['content_types'][$node_type] = $node_type_info['label'];
      $form['content_type'][$node_type]['content_type_value'] = [
        '#type' => 'select',
        '#title' => $node_type_info['label'],
        '#default_value' => $this->excludeNodeTitleManager->getBundleExcludeMode($node_type),
        '#options' => [
          'none' => $this->t('None'),
          'all' => $this->t('All nodes...'),
          'user' => $this->t('User defined nodes...'),
        ],
      ];

      $entity_view_modes = $this->entityDisplayRepository->getViewModes('node');
      $modes = [];
      foreach ($entity_view_modes as $view_mode_name => $view_mode_info) {
        $modes[$view_mode_name] = $view_mode_info['label'];
      }
      $modes += ['nodeform' => $this->t('Node form')];

      $title = match ($form['content_type'][$node_type]['content_type_value']['#default_value']) {
        'all' => $this->t('Exclude title from all nodes in the following view modes:'),
        'user defined' => $this->t('Exclude title from user defined nodes in the following view modes:'),
        default => $this->t('Exclude from:'),
      };

      $form['content_type'][$node_type]['content_type_modes'] = [
        '#type' => 'checkboxes',
        '#title' => $title,
        '#default_value' => $this->excludeNodeTitleManager->getExcludedViewModes($node_type),
        '#options' => $modes,
        '#states' => [
          // Hide the modes when the content type value is <none>.
          'invisible' => [
            'select[name="content_type[' . $node_type . '][content_type_value]"]' => [
              'value' => 'none',
            ],
          ],
        ],
      ];
    }

    $form['#attached']['library'][] = 'exclude_node_title/drupal.exclude_node_title.admin';

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $config = $this->configFactory->getEditable('exclude_node_title.settings');
    $values = $form_state->getValues();
    foreach ($values['content_type'] as $node_type => $value) {
      $modes = array_filter($values['content_type'][$node_type]['content_type_modes']);
      $modes = array_keys($modes);

      $config
        ->set('content_types.' . $node_type, $values['content_type'][$node_type]['content_type_value'])
        ->set('content_type_modes.' . $node_type, $modes);
    }

    $config
      ->set('search', $values['exclude_node_title_search'])
      ->set('type', $values['render_type'])
      ->save();

    parent::submitForm($form, $form_state);

    foreach (Cache::getBins() as $cache_backend) {
      $cache_backend->deleteAll();
    }
  }

}
