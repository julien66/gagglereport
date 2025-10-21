<?php

namespace Drupal\cookies\Services;

use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\language\ConfigurableLanguageManagerInterface;

/**
 * Services to handle module config and method for a rendered documentation.
 */
class CookiesConfigService {

  /**
   * Drupal\Core\Entity\EntityTypeManagerInterface definition.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Drupal\language\ConfigurableLanguageManagerInterface definition.
   *
   * @var \Drupal\language\ConfigurableLanguageManagerInterface
   */
  protected $languageManager;

  /**
   * Service groups.
   *
   * @var array
   */
  protected $serviceGroups;

  /**
   * Library discovery service.
   *
   * @var \Drupal\Core\Asset\LibraryDiscoveryInterface
   */
  protected $libraryDiscovery;

  /**
   * The base cookie configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $cookiesConfig;


  /**
   * The base cookie configuration.
   *
   * @var \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected $logger;

  /**
   * The base library where cookiejsr-preloader.js is load from.
   *
   * @var string
   */
  protected $baseLibrary;

  /**
   * Drupal\Core\Session\AccountProxyInterface definition.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * The documentation theme definition.
   */
  public const DOCUMENTATION_THEME_DEFINITION = 'cookies_docs';

  /**
   * The disclaimer theme definition.
   */
  public const DISCLAIMER_THEME_DEFINITION = 'cookies_docs_disclaimer';

  /**
   * Constructs a new CookiesConfigService object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, ConfigFactoryInterface $config_factory, ConfigurableLanguageManagerInterface $language_manager, LibraryDiscoveryInterface $library_discovery, LoggerChannelInterface $logger, AccountProxyInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->languageManager = $language_manager;
    $this->libraryDiscovery = $library_discovery;
    $this->configFactory = $config_factory;
    $this->cookiesConfig = $this->configFactory->get('cookies.config');
    $this->logger = $logger;
    $this->currentUser = $current_user;
  }

  /**
   * Returns the complete formatted Cookies JSR configuration.
   *
   * @param null|string $lang_id
   *   Optional parameter.
   *
   * @return array
   *   Complete formatted Cookies JSR configuration.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getCookiesConfig($lang_id = NULL) {
    // Pre-config language to deliver.
    $current_language = $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT);
    $lang_id = (!$lang_id) ? $current_language->getId() : $lang_id;
    $language = $this->languageManager->getLanguage($lang_id);
    $this->languageManager->setConfigOverrideLanguage($language);

    $service_groups = $this->getGroups();
    $ui_translation = $this->getTranslation($language);

    $group_translation = $this->getGroupTranslation(array_keys($service_groups));

    // Reset language to default.
    $this->languageManager->setConfigOverrideLanguage($current_language);

    return [
      "config" => $this->getConfig(),
      // @todo Rename this to "service_groups" as these are NOT services
      // entities but service groups:
      "services" => $service_groups,
      "translation" => array_merge($ui_translation, $group_translation),
    ];
  }

  /**
   * Retreives cookies module data, containing module configs and entities.
   *
   * @param string|null $lang_id
   *   The language id for translation data.
   *
   * @return array
   *   An array containing configurations needed by cookiesjsr, cookies text
   *   configurations and all cookies service and group entities as an array.
   */
  public function getCookiesModuleData($lang_id = NULL) {
    $cookiesTexts = $this->configFactory->get('cookies.texts')->get();
    $cookiesServicesArray = [];
    $cookiesGroupsArray = [];
    $cookieServiceObjects = $this->entityTypeManager->getStorage('cookies_service')->loadMultiple();
    $cookieGroupObjects = $this->entityTypeManager->getStorage('cookies_service_group')->loadMultiple();
    foreach ($cookieServiceObjects as $cookieServiceObject) {
      $cookiesServicesArray[$cookieServiceObject->id()] = $cookieServiceObject->toArray();
    }
    foreach ($cookieGroupObjects as $cookieGroupObject) {
      $cookiesGroupsArray[$cookieGroupObject->id()] = $cookieGroupObject->toArray();
    }
    return [
      'cookiesjsr' => $this->getCookiesConfig($lang_id),
      'cookiesTexts' => $cookiesTexts,
      'services' => $cookiesServicesArray,
      'groups' => $cookiesGroupsArray,
    ];
  }

  /**
   * Tests if the Cookies JSR library is installed in the correct folder.
   *
   * @return bool
   *   Test result if the library is installed correct.
   */
  protected function libraryExists() {
    $lib = $this->libraryDiscovery->getLibraryByName('cookies', 'cookiesjsr');
    if (!$lib) {
      return FALSE;
    }
    foreach ($lib['js'] as $js_file) {
      if (isset($js_file['data']) && $js_file['data']) {
        return file_exists(DRUPAL_ROOT . '/' . $js_file['data']);
      }
    }
    return FALSE;
  }

  /**
   * Returns the library to use (cdn or from library folder).
   *
   * @return string
   *   The library id as defined in the cookies.libraries.yml.
   */
  public function getLibrary() {
    if ($this->baseLibrary) {
      return $this->baseLibrary;
    }
    $loadCDN = (bool) $this->cookiesConfig->get('lib_load_from_cdn');
    if (!$loadCDN) {
      if ($this->libraryExists()) {
        return $this->baseLibrary = 'cookies/cookiesjsr';
      }
      $this->logger->error('Library still loads from CDN. You disabled to load Cookies JSR from CDN but you didn\'t install the library locally. Please download the library with version >=1.0.13 using Composer or from GitHub (https://github.com/jfeltkamp/cookiesjsr/tree/1.0.13) and place it into the libraries folder ({docroot}/libraries/cookiesjsr/dist/cookiesjsr.min.js) for loading from local');
    }
    return $this->baseLibrary = 'cookies/cookiesjsr.cdn';
  }

  /**
   * Returns the URL path from where the library is delivered.
   *
   * @return string
   *   Return library URL without file specification (fallback to CDN).
   */
  public function getLibraryUrl() {
    $libId = explode('/', $this->getLibrary());
    $lib = $this->libraryDiscovery->getLibraryByName($libId[0], $libId[1]);
    foreach ($lib['js'] as $js_file) {
      $libUrl = explode('/', $js_file['data']);
      array_pop($libUrl);
      $base = ($libId[1] != 'cookiesjsr.cdn') ? base_path() : '';
      return $base . implode('/', $libUrl);
    }
    return 'https://cdn.jsdelivr.net/gh/jfeltkamp/cookiesjsr@1/dist';
  }

  /**
   * Returns the configuration object for Cookies JSR.
   *
   * @return array[]
   *   Configuration object.
   */
  protected function getConfig() {
    $config = $this->cookiesConfig;

    $expires = (int) $config->get('cookie_expires');
    $expires = $expires * 24 * 60 * 60 * 1000;
    // The cookiesjsr library can not handle "0" as an expiration date and falls
    // back to 1 year instead. To define the cookie as a session cookie, we have
    // to explicitly set the expiration date to "NULL" here, see
    // https://www.drupal.org/project/cookies/issues/3343952:
    if ($expires === 0) {
      $expires = NULL;
    }
    // @todo config 'callback_method' and 'callback_url' to be removed.
    $callback = [
      "method" => $config->get('callback_method') ?? 'post',
      "url" => $config->get('callback_url')
      ?? Url::fromRoute('cookies.callback')->toString(),
      "headers" => [],
    ];

    $availableLangs = [];
    foreach ($this->languageManager->getLanguages() as $key => $lang) {
      $availableLangs[] = $key;
    }
    $defaultLang = $this->languageManager->getDefaultLanguage()->getId();

    $libPath = $this->getLibraryUrl();

    return [
      "cookie" => [
        "name" => $config->get('cookie_name'),
        "expires" => $expires,
        "domain" => $config->get('cookie_domain'),
        "sameSite" => $config->get('cookie_same_site'),
        "secure" => (bool) $config->get('cookie_secure'),
      ],
      "library" => [
        "libBasePath" => $libPath,
        "libPath" => $libPath . '/cookiesjsr.min.js',
        "scrollLimit" => (int) $config->get('lib_scroll_limit'),
      ],
      "callback" => $callback,
      "interface" => [
        "openSettingsHash" => "#{$config->get('open_settings_hash')}",
        "showDenyAll" => (bool) $config->get('show_deny_all'),
        "denyAllOnLayerClose" => (bool) $config->get('deny_all_on_layer_close'),
        "settingsAsLink" => (bool) $config->get('settings_as_link'),
        "availableLangs" => $availableLangs,
        "defaultLang" => $defaultLang,
        "groupConsent" => (bool) $config->get('group_consent'),
        "cookieDocs" => (bool) $config->get('cookie_docs'),
      ],
    ];
  }

  /**
   * Lazy loader for service groups.
   *
   * @return array|\Drupal\Core\Entity\EntityInterface[]
   *   Service groups.
   */
  protected function getServiceGroups() {
    if (!$this->serviceGroups) {
      try {
        $this->serviceGroups = $this->entityTypeManager->getStorage('cookies_service_group')->loadMultiple();
      }
      catch (\Exception $exception) {
        // @codingStandardsIgnoreStart
        // Disabled PHPCS warning because this is just an exception.
        \Drupal::logger('cookies')->error($exception->getMessage());
        // @codingStandardsIgnoreEnd
      }
    }
    return $this->serviceGroups;
  }

  /**
   * Returns a single cookie_service_group entity by ID.
   *
   * @param string $group
   *   Group seeking for.
   *
   * @return bool|\Drupal\Core\Entity\EntityInterface
   *   Return group Entity or false if none exists.
   */
  public function getGroup($group) {
    $groups = $this->getServiceGroups();
    return ($groups && isset($groups[$group])) ? $groups[$group] : FALSE;
  }

  /**
   * Get weight of a group for sort order.
   *
   * @param string $group
   *   Group seeking for.
   *
   * @return int
   *   The group weight
   */
  protected function getGroupWeight($group) {
    $group = $this->getGroup($group);
    return ($group) ? (int) $group->get('weight') : 99;
  }

  /**
   * Compare function to sort by weight.
   *
   * @param int $a
   *   Compare param.
   * @param int $b
   *   Compare param.
   *
   * @return int|\lt
   *   Result of comparison.
   */
  protected function sortWeight($a, $b) {
    return strcmp($a['weight'], $b['weight']);
  }

  /**
   * Returns the complete groups config.
   *
   * @return array
   *   Complete groups config
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  protected function getGroups() {
    $service_entities = $this->entityTypeManager
      ->getStorage('cookies_service')
      ->loadByProperties(['status' => 1]);
    $services = [];
    foreach ($service_entities as $s_entity) {
      /** @var \Drupal\cookies\Entity\CookiesServiceEntity $s_entity */
      $group = $s_entity->get('group');
      if (!isset($services[$group])) {
        $services[$group] = [
          "id" => $group,
          "services" => [],
          "weight" => $this->getGroupWeight($group),
        ];
      }
      $services[$group]['services'][] = [
        'key' => $s_entity->id(),
        'type' => $s_entity->get('group'),
        'name' => $s_entity->label(),
        'info' => $s_entity->get('info'),
        'uri' => $s_entity->get('processorUrl'),
        'needConsent' => $s_entity->get('consentRequired'),
      ];
    }

    uasort($services, [$this, "sortWeight"]);
    return $services;
  }

  /**
   * Returns translations of groups title and details.
   *
   * @param array $group_ids
   *   Collected group ids.
   *
   * @return array
   *   Returns array with group translation.
   */
  protected function getGroupTranslation(array $group_ids) {
    $translation = [];
    foreach ($group_ids as $group_id) {
      if ($group = $this->getGroup($group_id)) {
        $translation[$group_id] = [
          "title" => $group->get('title'),
          "details" => $group->get('details'),
        ];
      };
    }
    return $translation;
  }

  /**
   * Get UI translation.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language the translation is to deliver.
   *
   * @return array|mixed
   *   Complete translation of the config cookies.texts.
   */
  protected function getTranslation(LanguageInterface $language) {
    $config = $this->configFactory->get('cookies.texts')->get();
    $uris = ['privacyUri', 'imprintUri', 'cookieDocsUri'];
    foreach ($uris as $uri) {
      if (preg_match('/\/node\/([0-9]*)$/', $config[$uri], $matches)) {
        $url = Url::fromRoute('entity.node.canonical', ['node' => $matches[1]], ['language' => $language]);
        $config[$uri] = $url->toString(TRUE)->getGeneratedUrl();
      }
    }
    return $config;
  }

  /**
   * Renders the documentation listing.
   *
   * @return array
   *   Rendered cookie documentation listing.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getRenderedCookiesDocs($lang_id = NULL): array {
    $container = [
      '#type' => 'container',
      '#attributes' => ['id' => 'cookies-docs'],
      '#cache' => [
        'max-age' => Cache::PERMANENT,
        'contexts' => ['languages', 'user.roles'],
        'tags' => [
          'config:cookies.config',
          'config:cookies.texts',
          'config:cookies.cookies_service',
          'config:cookies.cookies_service_group',
        ],
      ],
    ];
    $cookiesDocs = [
      '#theme' => 'cookies_docs',
      '#groups' => $this->getGroupsRenderArray(),
      '#disclaimer' => $this->getDisclaimerRenderArray(),
    ];
    $container['cookies_docs'] = $cookiesDocs;
    return $container;
  }

  /**
   * Get disclaimer render array.
   */
  public function getDisclaimerRenderArray() {
    $cookiesTexts = $this->configFactory->get('cookies.texts');
    return [
      '#theme' => static::DISCLAIMER_THEME_DEFINITION,
      '#disclaimerText' => $cookiesTexts->get('disclaimerText'),
      '#disclaimerTextPosition' => $cookiesTexts->get('disclaimerTextPosition'),
    ];
  }

  /**
   * Get all service groups, that have associated service entities as an array.
   */
  public function getGroupsRenderArray() {
    $renderArray = [];
    $groupEntities = $this->getServiceGroups();
    foreach ($groupEntities as $groupEntity) {
      /** @var \Drupal\cookies\Entity\CookiesServiceGroup $groupEntity */
      $groupRenderArray = $groupEntity->toRenderArray();
      if (!empty($groupRenderArray['#services'])) {
        $renderArray[$groupEntity->id()] = $groupRenderArray;
      }
    }
    uasort($renderArray, function ($a, $b) {
      strcmp($a['#weight'], $b['#weight']);
    });
    return $renderArray;
  }

}
