<?php

namespace Drupal\cookies\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Defines the Cookie service entity entity.
 *
 * @ConfigEntityType(
 *   id = "cookies_service",
 *   label = @Translation("COOKiES service"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\cookies\CookiesServiceEntityListBuilder",
 *     "form" = {
 *       "add" = "Drupal\cookies\Form\CookiesServiceEntityForm",
 *       "edit" = "Drupal\cookies\Form\CookiesServiceEntityForm",
 *       "delete" = "Drupal\cookies\Form\CookiesServiceEntityDeleteForm"
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\cookies\CookiesRouteProvider",
 *     },
 *   },
 *   config_prefix = "cookies_service",
 *   admin_permission = "administer cookies services and service groups",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   config_export = {
 *     "langcode",
 *     "id",
 *     "label",
 *     "status",
 *     "dependencies",
 *     "group",
 *     "info",
 *     "consentRequired",
 *     "purpose",
 *     "processor",
 *     "processorContact",
 *     "processorUrl",
 *     "processorPrivacyPolicyUrl",
 *     "processorCookiePolicyUrl",
 *     "placeholderMainText",
 *     "placeholderAcceptText",
 *   },
 *   links = {
 *     "canonical" = "/admin/config/system/cookies/cookies-service/{cookies_service}",
 *     "add-form" = "/admin/config/system/cookies/cookies-service/add",
 *     "edit-form" = "/admin/config/system/cookies/cookies-service/{cookies_service}/edit",
 *     "delete-form" = "/admin/config/system/cookies/cookies-service/{cookies_service}/delete",
 *     "collection" = "/admin/config/system/cookies/cookies-service"
 *   }
 * )
 */
class CookiesServiceEntity extends ConfigEntityBase implements CookiesServiceEntityInterface {
  use StringTranslationTrait;

  /**
   * The Cookie service entity ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Cookie service entity label.
   *
   * @var string
   */
  protected $label;

  /**
   * The Cookie service group the service belongs to.
   *
   * @var string
   */
  protected $group;

  /**
   * Determines if the service requires consent.
   *
   * @var bool
   */
  protected $consentRequired;

  /**
   * The Cookie service info rich text (WYSIWYG).
   *
   * @var array
   */
  protected $info;

  /**
   * The purpose of this service.
   *
   * @var string
   */
  protected $purpose;

  /**
   * The processor / providing company of this service.
   *
   * @var string
   */
  protected $processor;

  /**
   * The data protection contact details of the processor company.
   *
   * @var string
   */
  protected $processorContact;

  /**
   * The processor's url.
   *
   * @var string
   */
  protected $processorUrl;

  /**
   * The processor's privacy policy url.
   *
   * @var string
   */
  protected $processorPrivacyPolicyUrl;

  /**
   * The processor's cookie policy url.
   *
   * @var string
   */
  protected $processorCookiePolicyUrl;

  /**
   * The displayed placeholder message, if the service is blocked.
   *
   * @var string
   */
  protected $placeholderMainText;

  /**
   * The displayed accept message, to only accept this service's cookies.
   *
   * @var string
   */
  protected $placeholderAcceptText;

  /**
   * The theme definition.
   */
  public const THEME_DEFINITION = 'cookies_docs_service';

  /**
   * Get the Cookie service group the service belongs to.
   */
  public function getGroup() {
    return $this->group;
  }

  /**
   * Get the Cookie service consent variable.
   */
  public function getConsentRequired() {
    return $this->consentRequired;
  }

  /**
   * Get the Cookie service info.
   */
  public function getInfo() {
    return $this->info;
  }

  /**
   * Get the purpose of this service.
   *
   * @return string
   *   The service's purpose.
   */
  public function getPurpose() {
    return $this->purpose;
  }

  /**
   * Get the processor / providing company of this service.
   *
   * @return string
   *   The service's processor.
   */
  public function getProcessor() {
    return $this->processor;
  }

  /**
   * Get the data protection contact details of the processor company.
   *
   * @return string
   *   The service's processorContact.
   */
  public function getProcessorContact() {
    return $this->processorContact;
  }

  /**
   * Get the provider url.
   *
   * @return string
   *   The service's processorUrl.
   */
  public function getProcessorUrl() {
    return $this->processorUrl;
  }

  /**
   * Get the provider's privacy policy url.
   *
   * @return string
   *   The service's processorPrivacyPolicyUrl.
   */
  public function getProcessorPrivacyPolicyUrl() {
    return $this->processorPrivacyPolicyUrl;
  }

  /**
   * Get the provider's cookie policy url.
   *
   * @return string
   *   The service's processorCookiePolicyUrl.
   */
  public function getProcessorCookiePolicyUrl() {
    return $this->processorCookiePolicyUrl;
  }

  /**
   * Get the displayed placeholder text, if the service is blocked.
   *
   * @return string
   *   The blocked placeholder text.
   */
  public function getPlaceholderMainText() {
    return $this->placeholderMainText;
  }

  /**
   * Get the displayed accept text, to only accept this service's cookies.
   *
   * @return string
   *   The accept text.
   */
  public function getPlaceholderAcceptText() {
    return $this->placeholderAcceptText;
  }

  /**
   * Set the Cookie service entity group.
   *
   * @param string $group
   *   The Cookie service entity group.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setGroup(string $group) {
    $this->group = $group;

    return $this;
  }

  /**
   * Set the Cookie service entity consent.
   *
   * @param bool $consent
   *   The Cookie service entity consent.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setConsentRequired(bool $consent) {
    $this->consentRequired = $consent;

    return $this;
  }

  /**
   * Set the Cookie service entity info.
   *
   * @param array $info
   *   The Cookie service entity info.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setInfo(array $info) {
    $this->info = $info;

    return $this;
  }

  /**
   * Set the provider's cookie policy url.
   *
   * @param string $processorCookiePolicyUrl
   *   The provider's cookie policy url.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setProcessorCookiePolicyUrl(string $processorCookiePolicyUrl) {
    $this->processorCookiePolicyUrl = $processorCookiePolicyUrl;

    return $this;
  }

  /**
   * Set the purpose of this service.
   *
   * @param string $purpose
   *   The purpose of this service.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setPurpose(string $purpose) {
    $this->purpose = $purpose;

    return $this;
  }

  /**
   * Set the processor / providing company of this service.
   *
   * @param string $processor
   *   The processor / providing company of this service.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setProcessor(string $processor) {
    $this->processor = $processor;

    return $this;
  }

  /**
   * Set the data protection contact details of the processor company.
   *
   * @param string $processorContact
   *   The data protection contact details of the processor company.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setProcessorContact(string $processorContact) {
    $this->processorContact = $processorContact;

    return $this;
  }

  /**
   * Set the provider url.
   *
   * @param string $processorUrl
   *   The provider url.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setProcessorUrl(string $processorUrl) {
    $this->processorUrl = $processorUrl;

    return $this;
  }

  /**
   * Set the provider's privacy policy url.
   *
   * @param string $processorPrivacyPolicyUrl
   *   The provider's privacy policy url.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setProcessorPrivacyPolicyUrl(string $processorPrivacyPolicyUrl) {
    $this->processorPrivacyPolicyUrl = $processorPrivacyPolicyUrl;

    return $this;
  }

  /**
   * Set the displayed placeholder main text, if the service is blocked.
   *
   * @param string $placeholderMainText
   *   The displayed placeholder main text, if the service is blocked.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setPlaceholderMainText(string $placeholderMainText) {
    $this->placeholderMainText = $placeholderMainText;

    return $this;
  }

  /**
   * Set the displayed accept text, to only accept this service's cookies.
   *
   * @param string $placeholderAcceptText
   *   The displayed accept text, to only accept this service's cookies.
   *
   * @return self
   *   This cookies service entity.
   */
  public function setPlaceholderAcceptText(string $placeholderAcceptText) {
    $this->placeholderAcceptText = $placeholderAcceptText;

    return $this;
  }

  /**
   * Returns the render array representation of the service entity.
   */
  public function toRenderArray(): array {
    $textsConfig = \Drupal::service('config.factory')->get('cookies.texts');
    $info = $this->getInfo();
    $renderArray = [
      '#theme' => static::THEME_DEFINITION,
      '#id' => $this->id(),
      '#label' => $this->label(),
      '#group' => $this->getGroup(),
      '#info' => [
        '#type' => 'processed_text',
        '#text' => $info['value'],
        '#format' => $info['format'],
      ],
      '#consentRequired' => $this->getConsentRequired(),
      '#purpose' => $this->getPurpose(),
      '#processor' => $this->getProcessor(),
      '#processorContact' => $this->getProcessorContact(),
      '#processorUrl' => $this->getProcessorUrl(),
      '#processorPrivacyPolicyUrl' => $this->getProcessorPrivacyPolicyUrl(),
      '#processorCookiePolicyUrl' => $this->getProcessorCookiePolicyUrl(),
      '#attributes' => [
        'processorDetailsLabel' => $textsConfig->get('processorDetailsLabel') ?? 'Processor Company Details',
        'processorLabel' => $textsConfig->get('processorLabel') ?? 'Company',
        'processorWebsiteUrlLabel' => $textsConfig->get('processorWebsiteUrlLabel') ?? 'Company Website',
        'processorPrivacyPolicyUrlLabel' => $textsConfig->get('processorPrivacyPolicyUrlLabel') ?? 'Company Privacy Policy',
        'processorCookiePolicyUrlLabel' => $textsConfig->get('processorCookiePolicyUrlLabel') ?? 'Company Cookie Policy',
        'processorContactLabel' => $textsConfig->get('processorContactLabel') ?? 'Data Protection Contact Details',
        'edit_link_text' => $this->t('Edit cookie information for @label here', ['@label' => $this->label()]),
      ],
      '#contextual_links' => [
        'block' => [
          'route_parameters' => ['block' => $this->id()],
        ],
      ],
    ];
    return $renderArray;
  }

}
