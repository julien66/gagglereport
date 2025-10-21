<?php

namespace Drupal\cookies\Form;

use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\path_alias\AliasManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Configure site information settings for this site.
 *
 * @internal
 */
class CookiesTextsForm extends ConfigFormBase {

  /**
   * The path alias manager.
   *
   * @var \Drupal\path_alias\AliasManagerInterface
   */
  protected $aliasManager;

  /**
   * The path validator.
   *
   * @var \Drupal\Core\Path\PathValidatorInterface
   */
  protected $pathValidator;

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected $requestContext;

  /**
   * The cache tag invalidator.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidator
   */
  protected $cacheTagsInvalidator;

  /**
   * Constructs a SiteInformationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The request context.
   * @param \Drupal\Core\Cache\CacheTagsInvalidator $cacheTagsInvalidator
   *   The cache tag invalidator.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface|null $typedConfigManager
   *   The typed configuration manager or null for older drupal versions.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AliasManagerInterface $alias_manager, PathValidatorInterface $path_validator, RequestContext $request_context, CacheTagsInvalidator $cacheTagsInvalidator, ?TypedConfigManagerInterface $typedConfigManager = NULL) {
    parent::__construct($config_factory, $typedConfigManager);

    if (!$alias_manager instanceof AliasManagerInterface) {
      // @codingStandardsIgnoreStart
      // Disabled PHPCS warning because this is just a deprecation fallback.
      $alias_manager = \Drupal::service('path_alias.manager');
      // @codingStandardsIgnoreEnd
    }
    $this->aliasManager = $alias_manager;
    $this->pathValidator = $path_validator;
    $this->requestContext = $request_context;
    $this->cacheTagsInvalidator = $cacheTagsInvalidator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('path_alias.manager'),
      $container->get('path.validator'),
      $container->get('router.request_context'),
      $container->get('cache_tags.invalidator'),
      $container->get('config.typed') ?? NULL,
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'cookies_text_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['cookies.texts'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('cookies.texts');

    $form['banner'] = [
      '#type' => 'details',
      '#title' => $this->t('Banner texts'),
      '#open' => TRUE,
    ];
    $form['banner']['bannerText'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Banner details'),
      '#default_value' => $config->get('bannerText'),
      '#required' => TRUE,
    ];

    $form['links'] = [
      '#type' => 'details',
      '#title' => $this->t('Links'),
      '#open' => TRUE,
    ];
    $form['links']['privacyPolicy'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Privacy Policy Label'),
      '#default_value' => $config->get('privacyPolicy'),
      '#description' => $this->t('Link label for privacy policy link (Default: "Privacy policy")'),
    ];
    $form['links']['privacyUri'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Privacy Policy URI'),
      '#default_value' => $config->get('privacyUri'),
      '#description' => $this->t('Link path (int./ext.) for privacy policy link, e.g. "/privacy" (int.) or "https://www.example.com/privacy" (ext.) (Default: "")'),
    ];
    $form['links']['imprint'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Imprint Label'),
      '#default_value' => $config->get('imprint'),
      '#description' => $this->t('Link title for imprint link (Default: "Imprint")'),
    ];
    $form['links']['imprintUri'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Imprint URI'),
      '#default_value' => $config->get('imprintUri'),
      '#description' => $this->t('Link path (int./ext.) for imprint link, e.g. "/imprint" (int.) or "https://www.example.com/imprint" (ext.) (Default: "")'),
    ];
    $form['links']['cookieDocs'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Cookie Documentation Label'),
      '#default_value' => $config->get('cookieDocs'),
      '#description' => $this->t('Link text for a cookie documentation page (Default: "Cookie documentation")'),
    ];
    $form['links']['cookieDocsUri'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Cookie Documentation URI'),
      '#default_value' => $config->get('cookieDocsUri'),
      '#description' => $this->t('URL for a Cookie Documentation page where explicitly is described what 3rd-party services and cookies are used. This is required, if you use "Group consent". The default cookies documentation is also provided as a block, if you want to attach these information to an existing page. (Default: "/cookies/documentation")'),
    ];
    $form['links']['officialWebsite'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Cookie Official Website Label'),
      '#default_value' => $config->get('officialWebsite'),
      '#description' => $this->t('Link text for a cookie "external processor company URL" link (Default: "Official website")'),
    ];
    $form['buttons'] = [
      '#type' => 'details',
      '#title' => $this->t('Button labels'),
      '#open' => TRUE,
    ];
    $form['buttons']['acceptAll'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Accept all'),
      '#default_value' => $config->get('acceptAll'),
      '#description' => $this->t('Button label for "Accept all" (Default: "Accept all")'),
    ];
    $form['buttons']['denyAll'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Deny all'),
      '#default_value' => $config->get('denyAll'),
      '#description' => $this->t('Button label for "Deny all" (Default: "Deny all")'),
    ];
    $form['buttons']['settings'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Settings'),
      '#default_value' => $config->get('settings'),
      '#description' => $this->t('Button label for "Settings" (Default: "Settings")'),
    ];
    $form['buttons']['saveSettings'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Save Settings'),
      '#default_value' => $config->get('saveSettings'),
      '#description' => $this->t('Button label for the save button (Default: "Save")'),
    ];

    $form['dialog'] = [
      '#type' => 'details',
      '#title' => $this->t('Dialog texts'),
      '#open' => TRUE,
    ];
    $form['dialog']['cookieSettings'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Dialog title (Default: "Cookie settings")'),
      '#default_value' => $config->get('cookieSettings'),
      '#required' => TRUE,
    ];
    $form['dialog']['close'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Close button hover label'),
      '#default_value' => $config->get('close'),
      '#description' => $this->t('Close button hover label. (Default: "Close")'),
    ];
    $form['dialog']['allowed'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Allowed label'),
      '#default_value' => $config->get('allowed'),
      '#description' => $this->t('Switch title hover label. (Default: "Allowed")'),
    ];
    $form['dialog']['denied'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Denied label'),
      '#default_value' => $config->get('denied'),
      '#description' => $this->t('Switch title hover label. (Default: "Denied")'),
    ];
    // @todo We should remove this in the future, as this is handled by the
    // appropriate service
    // (https://www.drupal.org/project/cookies/issues/3325500):
    $form['dialog']['requiredCookies'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Required cookies label'),
      '#default_value' => $config->get('requiredCookies'),
      '#description' => $this->t('Text for "Required cookies" with grouped consent. (Default: "Required cookies")'),
    ];
    $form['dialog']['readMore'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Read more label'),
      '#default_value' => $config->get('readMore'),
      '#description' => $this->t('Read more link text. (Default: "Read more")'),
    ];
    $form['dialog']['alwaysActive'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Always active label'),
      '#default_value' => $config->get('alwaysActive'),
      '#description' => $this->t('Label replaces switch when service is always active. (Default: "Always active")'),
    ];
    $form['dialog']['settingsAllServices'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Settings for all services label'),
      '#default_value' => $config->get('settingsAllServices'),
      '#description' => $this->t('Dialog footer, label for actions. (Default: "Settings for all services")'),
    ];

    $form['cookies_services_documentation'] = [
      '#type' => 'details',
      '#title' => $this->t('COOKiES services documentation texts'),
      '#description' => $this->t('Texts for the COOKiES services documentation (page, token, settings layer).'),
      '#open' => TRUE,
    ];
    $form['cookies_services_documentation']['processorDetailsLabel'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Processor Company Details Label'),
      '#default_value' => $config->get('processorDetailsLabel'),
      '#description' => $this->t('The processor company details label. (Default: "Processor Company Details")'),
    ];
    $form['cookies_services_documentation']['processorLabel'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Processor Company Label'),
      '#default_value' => $config->get('processorLabel'),
      '#description' => $this->t('The processor company label. (Default: "Company")'),
    ];
    $form['cookies_services_documentation']['processorWebsiteUrlLabel'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Processor Website URL Label'),
      '#default_value' => $config->get('processorWebsiteUrlLabel'),
      '#description' => $this->t('The processor website url label. (Default: "Company Website")'),
    ];
    $form['cookies_services_documentation']['processorPrivacyPolicyUrlLabel'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Processor Privacy Policy URL Label'),
      '#default_value' => $config->get('processorPrivacyPolicyUrlLabel'),
      '#description' => $this->t('The processor privacy policy url label. (Default: "Company Privacy Policy")'),
    ];
    $form['cookies_services_documentation']['processorCookiePolicyUrlLabel'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Processor Cookie Policy URL Label'),
      '#default_value' => $config->get('processorCookiePolicyUrlLabel'),
      '#description' => $this->t('The processor cookie policy url label. (Default: "Company Cookie Policy")'),
    ];
    $form['cookies_services_documentation']['processorContactLabel'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Processor Data Protection Contact Details Label'),
      '#default_value' => $config->get('processorContactLabel'),
      '#description' => $this->t('The processor data protection contact details label. (Default: "Data Protection Contact Details")'),
    ];
    $form['cookies_services_documentation']['disclaimerText'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('COOKiES documentation disclaimer'),
      '#description' => $this->t('Allows to place a disclaimer text above and / or below the COOKiES services documentation output. Leave empty to show no disclaimer. Ensure you update service information regularly anyway! (Default: "All cookie information is subject to change by the service providers. We update this information regularly.")'),
      '#default_value' => $config->get('disclaimerText'),
    ];
    $form['cookies_services_documentation']['disclaimerTextPosition'] = [
      '#type' => 'select',
      '#title' => $this->t('COOKiES documentation disclaimer position'),
      '#options' => [
        'above' => $this->t('Above'),
        'below' => $this->t('Below'),
        'both' => $this->t('Both'),
      ],
      '#default_value' => $config->get('disclaimerTextPosition'),
      '#states' => [
        'invisible' => [
          ':input[name="disclaimerText"]' => ['value' => ''],
        ],
      ],
    ];

    $form['placeholder_texts'] = [
      '#type' => 'details',
      '#title' => $this->t('Global placeholder texts'),
      '#description' => $this->t('Global texts for the COOKiES services placeholder, which blocks service related elements.'),
      '#open' => TRUE,
    ];
    $form['placeholder_texts']['placeholderAcceptAllText'] = [
      '#type' => 'textfield',
      '#maxlength' => 255,
      '#title' => $this->t('Accept All Message'),
      '#description' => $this->t('The global placeholder accept all cookies text (Default: "Accept All Cookies")'),
      '#default_value' => $config->get('placeholderAcceptAllText'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {

    // Validate front page path.
    if (($value = $form_state->getValue('imprintUri')) && !preg_match('/^http(s)?:\/\//', $value) && $value[0] !== '/') {
      $form_state->setErrorByName('imprintUri', $this->t("The path '%path' has to start with a slash.", ['%path' => $form_state->getValue('imprintUri')]));
    }
    if (!preg_match('/^http(s)?:\/\//', $value) && !$this->pathValidator->isValid($form_state->getValue('imprintUri'))) {
      $form_state->setErrorByName('imprintUri', $this->t("Either the path '%path' is invalid or you do not have access to it.", ['%path' => $form_state->getValue('imprintUri')]));
    }
    // Get the normal paths of both error pages.
    if (!$form_state->isValueEmpty('imprintUri')) {
      $form_state->setValueForElement($form['links']['imprintUri'], $this->aliasManager->getPathByAlias($form_state->getValue('imprintUri')));
    }

    // Validate privacy uri.
    if (($value = $form_state->getValue('privacyUri')) && !preg_match('/^http(s)?:\/\//', $value) && $value[0] !== '/') {
      $form_state->setErrorByName('privacyUri', $this->t("The path '%path' has to start with a slash.", ['%path' => $form_state->getValue('privacyUri')]));
    }
    if (!preg_match('/^http(s)?:\/\//', $value) && !$this->pathValidator->isValid($form_state->getValue('privacyUri'))) {
      $form_state->setErrorByName('privacyUri', $this->t("Either the path '%path' is invalid or you do not have access to it.", ['%path' => $form_state->getValue('privacyUri')]));
    }
    // Get the normal paths of both error pages.
    if (!$form_state->isValueEmpty('privacyUri')) {
      $form_state->setValueForElement($form['links']['privacyUri'], $this->aliasManager->getPathByAlias($form_state->getValue('privacyUri')));
    }

    // Validate front page path.
    if (($value = $form_state->getValue('cookieDocsUri')) && !preg_match('/^http(s)?:\/\//', $value) && $value[0] !== '/') {
      $form_state->setErrorByName('cookieDocsUri', $this->t("The path '%path' has to start with a slash.", ['%path' => $form_state->getValue('cookieDocsUri')]));
    }
    if (!preg_match('/^http(s)?:\/\//', $value) && !$this->pathValidator->isValid($form_state->getValue('cookieDocsUri'))) {
      $form_state->setErrorByName('cookieDocsUri', $this->t("Either the path '%path' is invalid or you do not have access to it.", ['%path' => $form_state->getValue('cookieDocsUri')]));
    }
    // Get the normal paths of both error pages.
    if (!$form_state->isValueEmpty('cookieDocsUri')) {
      $form_state->setValueForElement($form['links']['cookieDocsUri'], $this->aliasManager->getPathByAlias($form_state->getValue('cookieDocsUri')));
    }

    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('cookies.texts')
      ->set('bannerText', $form_state->getValue('bannerText'))
      ->set('privacyPolicy', $form_state->getValue('privacyPolicy'))
      ->set('privacyUri', $form_state->getValue('privacyUri'))
      ->set('imprint', $form_state->getValue('imprint'))
      ->set('imprintUri', $form_state->getValue('imprintUri'))
      ->set('cookieDocs', $form_state->getValue('cookieDocs'))
      ->set('cookieDocsUri', $form_state->getValue('cookieDocsUri'))
      ->set('officialWebsite', $form_state->getValue('officialWebsite'))
      ->set('denyAll', $form_state->getValue('denyAll'))
      ->set('settings', $form_state->getValue('settings'))
      ->set('acceptAll', $form_state->getValue('acceptAll'))
      ->set('saveSettings', $form_state->getValue('saveSettings'))
      ->set('cookieSettings', $form_state->getValue('cookieSettings'))
      ->set('close', $form_state->getValue('close'))
      ->set('allowed', $form_state->getValue('allowed'))
      ->set('denied', $form_state->getValue('denied'))
      ->set('requiredCookies', $form_state->getValue('requiredCookies'))
      ->set('readMore', $form_state->getValue('readMore'))
      ->set('alwaysActive', $form_state->getValue('alwaysActive'))
      ->set('settingsAllServices', $form_state->getValue('settingsAllServices'))
      ->set('processorDetailsLabel', $form_state->getValue('processorDetailsLabel'))
      ->set('processorLabel', $form_state->getValue('processorLabel'))
      ->set('processorWebsiteUrlLabel', $form_state->getValue('processorWebsiteUrlLabel'))
      ->set('processorPrivacyPolicyUrlLabel', $form_state->getValue('processorPrivacyPolicyUrlLabel'))
      ->set('processorCookiePolicyUrlLabel', $form_state->getValue('processorCookiePolicyUrlLabel'))
      ->set('processorContactLabel', $form_state->getValue('processorContactLabel'))
      ->set('disclaimerText', $form_state->getValue('disclaimerText'))
      ->set('disclaimerTextPosition', $form_state->getValue('disclaimerTextPosition'))
      ->set('placeholderAcceptAllText', $form_state->getValue('placeholderAcceptAllText'))
      ->save();

    $this->cacheTagsInvalidator->invalidateTags(['config:cookies.texts']);

    parent::submitForm($form, $form_state);
  }

}
