<?php

namespace Drupal\cookies_filter\Form;

use Drupal\cookies_filter\Services\CookiesFilterElementTypesService;
use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to add and edit an CookiesServiceFilterEntity entity.
 */
class CookiesServiceFilterEntityForm extends EntityForm {


  /**
   * The famous Drupal Entity Type Manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The famous Drupal Cache Tags Invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidator
   */
  protected $cacheTagsInvalidator;

  /**
   * The cookies filter element types service.
   *
   * @var \Drupal\cookies_filter\Services\CookiesFilterElementTypesService
   */
  protected $cookiesFilterElementTypesService;

  /**
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, CacheTagsInvalidator $cache_tags_invalidator, CookiesFilterElementTypesService $cookiesFilterElementTypesService) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
    $this->cookiesFilterElementTypesService = $cookiesFilterElementTypesService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('entity_type.manager'),
      $container->get('cache_tags.invalidator'),
      $container->get('cookies_filter.element_types')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $cookie_service_filter_entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_filter_entity->label(),
      '#description' => $this->t("Label for the Cookie service filter entity."),
      '#required' => TRUE,
      '#weight' => 10,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $cookie_service_filter_entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\cookies_filter\Entity\CookiesServiceFilterEntity::load',
      ],
      '#disabled' => !$cookie_service_filter_entity->isNew(),
      '#weight' => 10,
    ];

    $form['entity'] = [
      '#type' => 'details',
      '#title' => $this->t('Properties'),
      '#open' => TRUE,
      '#weight' => 20,
    ];
    $services = $this->entityTypeManager->getStorage('cookies_service')->loadMultiple();
    $service_options = [];
    foreach ($services as $id => $entity) {
      $service_options[$id] = $entity->label();
    }
    $form['entity']['service'] = [
      '#type' => 'select',
      '#title' => $this->t('COOKiES Service'),
      '#options' => $service_options,
      '#default_value' => $cookie_service_filter_entity->get('service'),
      '#description' => $this->t("The service this filter belongs to,
        e.g. 'Google Analytics'. You may need to add new services if this is a filter
        for a not yet existing service."),
      '#required' => TRUE,
    ];

    $elementTypesSelectList = $this->cookiesFilterElementTypesService->getElementTypesSelectList();
    $form['entity']['elementType'] = [
      '#type' => 'radios',
      '#title' => $this->t('Element type'),
      '#options' => $elementTypesSelectList,
      '#default_value' => $cookie_service_filter_entity->get('elementType'),
      '#description' => $this->t("The element type to handle. Must be selected
      because the elements have different properties and have to be handled
      differently. If you need multiple elements for the same service, simply
      create multiple separate service filters.<br>NOTE: Inline Scripts
      (@example) are currently NOT supported! Use the 'script[src]' selector instead, see <a href='https://www.drupal.org/project/cookies/issues/3294942' target='_blank'>this issue</a> for more information.", ['@example' => '<script>...</script>']),
      '#required' => TRUE,
    ];

    $form['entity']['elementSelectors'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Element selectors'),
      '#rows' => 10,
      '#cols' => 60,
      '#default_value' => $cookie_service_filter_entity->get('elementSelectors'),
      '#description' => $this->t("CSS Selectors to filter the current element type, each row is chained by 'or'.<br><b>Note: If left blank, all instances of the selected element type will be blocked</b><br><br><b>Typical Selectors:</b><br>
                                  <ul>
                                  <li><code>iframe.my-class-name</code> -> iframe with a certain class</li>
                                  <li><code>iframe[src*='youtube.com']</code> -> iframes with source youtube.com</li>
                                  <li><code>script[src]#my-id</code> -> External script with ID</li>
                                  <li><code>div > iframe</code> -> All iframes within a div element</li>
                                  </ul>"),
      '#required' => FALSE,
    ];

    $form['entity']['placeholderBehaviour'] = [
      '#type' => 'select',
      '#title' => $this->t('Blocked element display (Placeholder)'),
      '#options' => [
        'overlay' => $this->t('Cookies overlay'),
        'hide' => $this->t('Hide element'),
        'none' => $this->t('None (keep as-is)'),
      ],
      '#default_value' => $this->entity->isNew() ? 'overlay' : $cookie_service_filter_entity->get('placeholderBehaviour'),
      '#description' => $this->t("Select if the blocked element should be
      replaced by a placeholder, hidden or kept as-is."),
      '#required' => TRUE,
    ];

    $form['entity']['placeholderCustomElementSelectors'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Placeholder element custom selectors'),
      '#rows' => 10,
      '#cols' => 60,
      '#default_value' => $cookie_service_filter_entity->get('placeholderCustomElementSelectors'),
      '#description' => $this->t("If specified, uses these selectors <strong>instead of the blocked element wrapper</strong>
      as placeholder target, each row is chained by 'or'. Leave empty to use the element (wrapper) as target automatically.<br> For example, if a blocked script creates a div element in the header with the id 'test' we can type in the selector '#test' to show the placeholder cookies overlay on the div element"),
      '#required' => FALSE,
      '#states' => [
        'invisible' => [
          ':input[name="placeholderBehaviour"]' => ['value' => 'none'],
        ],
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Empty custom selectors, if placeholderBehaviour is 'none'.
    if ($form_state->getValue('placeholderBehaviour') == 'none') {
      $form_state->setValue('placeholderCustomElementSelectors', '');
    }
    if ($elementSelectors = $form_state->getValue('elementSelectors')) {
      $elementType = $this->entity->get('elementType');
      $elementSelectorsArray = explode("\n", $elementSelectors);
      $selectorContainsType = TRUE;
      foreach ($elementSelectorsArray as $elementSelector) {
        if (strpos($elementSelector, $elementType) === FALSE) {
          $selectorContainsType = FALSE;
        }
      }
      if ($selectorContainsType === FALSE) {
        $form_state->setErrorByName('elementSelectors', $this->t("One of the selectors does not contain %element", ['%element' => $elementType]));
      }
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $cookie_service_filter_entity = $this->entity;
    $status = $cookie_service_filter_entity->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Cookie service filter entity.', [
          '%label' => $cookie_service_filter_entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Cookie service filter entity.', [
          '%label' => $cookie_service_filter_entity->label(),
        ]));
    }

    $this->cacheTagsInvalidator->invalidateTags(['config:cookies_filter.cookies_service_filter']);
    $form_state->setRedirectUrl($cookie_service_filter_entity->toUrl('collection'));
    return $status;
  }

}
