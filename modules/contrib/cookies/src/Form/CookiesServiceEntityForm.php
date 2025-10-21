<?php

namespace Drupal\cookies\Form;

use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form to add and edit an CookiesServiceEntity entity.
 */
class CookiesServiceEntityForm extends EntityForm {


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
   * Class constructor.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, CacheTagsInvalidator $cache_tags_invalidator) {
    $this->entityTypeManager = $entity_type_manager;
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
    // Load the service required to construct this class.
      $container->get('entity_type.manager'),
      $container->get('cache_tags.invalidator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /**
     * @var \Drupal\cookies\Entity\CookiesServiceEntity
     */
    $cookie_service_entity = $this->entity;

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->label(),
      '#description' => $this->t("Label for the Cookie service entity."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $cookie_service_entity->id(),
      '#machine_name' => [
        'exists' => '\Drupal\cookies\Entity\CookiesServiceEntity::load',
      ],
      '#disabled' => !$cookie_service_entity->isNew(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enabled'),
      '#default_value' => $cookie_service_entity->get('status'),
      '#description' => $this->t("If checkbox is enabled, entity is shown in the user consent management widget."),
    ];

    $form['consentRequired'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Consent required'),
      '#default_value' => $cookie_service_entity->getConsentRequired() ?? TRUE,
      '#description' => $this->t("When enabled, this service needs active user consent to be loaded.
        Based on the individual service (and its submodule) implementation, when enabled, the service is blocked until consent is given.
        When disabled, consent is assumed and the service typically isn't blocked.
        Disabling this is typically only recommended for required technical cookies and can lead to GDPR violations otherwise."),
    ];

    $form['group'] = [
      '#type' => 'select',
      '#title' => $this->t('Service group'),
      '#options' => $this->getCookiesServiceGroupsLabel(),
      '#default_value' => $cookie_service_entity->getGroup(),
      '#description' => $this->t("Group the service belongs to e.g. 'tracking'."),
      '#required' => TRUE,
    ];

    $form['purpose'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Purpose'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->getPurpose(),
      '#description' => $this->t("This service's purpose."),
      '#required' => FALSE,
    ];

    $cookie_service_entity_info = $cookie_service_entity->getInfo();
    $form['info'] = [
      '#type' => 'text_format',
      '#format' => $cookie_service_entity_info['format'] ?? NULL,
      '#title' => $this->t('Documentation'),
      '#default_value' => $cookie_service_entity_info['value'] ?? '',
      '#description' => $this->t('Local documentation for cookie details from this provider.'),
    ];

    $form['placeholder'] = [
      '#type' => 'details',
      '#title' => $this->t('Service Placeholder Texts'),
      '#description' => $this->t('Service specific placeholder texts for the COOKiES overlay, which blocks service specific elements.'),
      '#open' => TRUE,
    ];
    $form['placeholder']['placeholderMainText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Main Text'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->getPlaceholderMainText(),
      '#description' => $this->t('The placeholder main text (Example: "This content is blocked because XYZ cookies have not been accepted.").'),
      '#required' => TRUE,
    ];
    $form['placeholder']['placeholderAcceptText'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Accept Text'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->getPlaceholderAcceptText(),
      '#description' => $this->t('The placeholder accept text (Example: "Only accept XYZ cookies").'),
      '#required' => TRUE,
    ];

    $form['processorDetails'] = [
      '#type' => 'details',
      '#title' => $this->t('Processor Company Details'),
      '#description' => $this->t('Additional informations about the optional asscociated processor company of this service.'),
      '#open' => TRUE,
    ];

    $form['processorDetails']['processor'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Company'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->getProcessor(),
      '#description' => $this->t('The processor company of this service (Example: "Example LCC").'),
      '#required' => FALSE,
    ];

    $form['processorDetails']['processorContact'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Data Protection Contact Details'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->getProcessorContact(),
      '#description' => $this->t('The data protection contact details of the processor company (Example: "privacy@example.com").'),
      '#required' => FALSE,
    ];

    $form['processorDetails']['processorUrl'] = [
      '#type' => 'url',
      '#title' => $this->t('URL'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->getProcessorUrl(),
      '#description' => $this->t('External processor company URL (Example: "https://www.example.com/").'),
      '#required' => FALSE,
    ];

    $form['processorDetails']['processorPrivacyPolicyUrl'] = [
      '#type' => 'url',
      '#title' => $this->t('Privacy Policy URL'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->getProcessorPrivacyPolicyUrl(),
      '#description' => $this->t('External processor company privacy policy URL (Example: "https://policies.example.com/privacy").'),
      '#required' => FALSE,
    ];

    $form['processorDetails']['processorCookiePolicyUrl'] = [
      '#type' => 'url',
      '#title' => $this->t('Cookie Policy URL'),
      '#maxlength' => 255,
      '#default_value' => $cookie_service_entity->get('processorCookiePolicyUrl'),
      '#description' => $this->t('External processor company cookies policy URL (Example: "https://policies.example.com/cookies").'),
      '#required' => FALSE,
    ];

    return $form;
  }

  /**
   * Returns an array containing all cookie service group labels, keyed by id.
   *
   * @return array
   *   A cookies service group array.
   */
  protected function getCookiesServiceGroupsLabel() {
    $groups = $this->entityTypeManager->getStorage('cookies_service_group')->loadMultiple();
    $group_options = [];
    foreach ($groups as $id => $entity) {
      $group_options[$id] = $entity->label();
    }
    return $group_options;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $cookie_service_entity = $this->entity;
    $status = $cookie_service_entity->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Cookie service entity.', [
          '%label' => $cookie_service_entity->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Cookie service entity.', [
          '%label' => $cookie_service_entity->label(),
        ]));
    }

    $this->cacheTagsInvalidator->invalidateTags(['config:cookies.cookies_service']);
    $form_state->setRedirectUrl($cookie_service_entity->toUrl('collection'));
    return $status;
  }

}
