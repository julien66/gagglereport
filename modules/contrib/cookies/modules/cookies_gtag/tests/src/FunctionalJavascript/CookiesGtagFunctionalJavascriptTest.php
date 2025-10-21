<?php

namespace Drupal\Tests\cookies_gtag\FunctionalJavascript;

use Drupal\Core\Url;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\cookies\Traits\CookiesCacheClearTrait;

/**
 * Tests cookies_gtag Javascript related functionalities.
 *
 * @group cookies_gtag
 */
class CookiesGtagFunctionalJavascriptTest extends WebDriverTestBase {
  use CookiesCacheClearTrait;

  /**
   * An admin user with all permissions.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * The user.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $user;

  /**
   * A Google Tag manager container.
   *
   * @var \Drupal\google_tag\Entity\Container
   */
  protected $container;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'test_page_test',
    'filter_test',
    'block',
    'cookies',
    'google_tag',
    'cookies_gtag',
  ];

  /**
   * {@inheritDoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->config('system.site')->set('page.front', '/test-page')->save();
    $page = $this->getSession()->getPage();
    $this->user = $this->drupalCreateUser([]);
    $this->adminUser = $this->drupalCreateUser([]);
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    // Manually login the admin user:
    $this->drupalGet(Url::fromRoute('user.login'));
    $page->fillField('edit-name', $this->adminUser->getAccountName());
    $page->fillField('edit-pass', $this->adminUser->passRaw);
    $page->pressButton('edit-submit');

    // Test that scripts are knocked out even when JS aggregation is enabled.
    $this->config('system.performance')->set('js.preprocess', TRUE)->save();

    // Adjust default container google tag id:
    $this->drupalGet('/admin/config/services/google-tag');
    $page->fillField('edit-accounts-0-value', 'G-xxxxxxxx');
    $page->pressButton('edit-submit');
    $this->drupalPlaceBlock('cookies_ui_block');
    $this->clearBackendCaches();
  }

  /**
   * Tests if the scripts get knocked out through 'consentRequired'.
   *
   * Tests if the scripts get knocked out through the 'consentRequired'
   * cookies service entity setting.
   */
  public function testScriptKnockedOutThroughConsentRequired() {
    $session = $this->assertSession();
    // Service consent is required, both javascript files should be disabled
    // and the googletagmanager js shouldn't be displayed at all:
    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="/js/gtag.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.js"]', 'type', 'text/plain');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.js"]', 'data-cookieconsent', 'gtag');

    $session->elementsCount('css', 'script[src*="/js/gtag.ajax.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'type', 'text/plain');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'data-cookieconsent', 'gtag');

    $this->assertNull($session->waitForElement('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]'));
    $session->elementNotExists('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]');

    // Uncheck gtag service entity "consent required":
    $cookies_gtag_service_entity = \Drupal::entityTypeManager()
      ->getStorage('cookies_service')
      ->load('gtag');
    $cookies_gtag_service_entity->set('consentRequired', FALSE);
    $cookies_gtag_service_entity->save();

    \Drupal::service('cache_tags.invalidator')->invalidateTags([
      'config:cookies.cookies_service',
    ]);

    $this->clearBackendCaches();

    // Service consent is not required anymore, both js should be enabled now
    // and the googletagmanager.js should appear:
    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="/js/gtag.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.js"]', 'type', '');
    $session->elementAttributeExists('css', 'script[src*="/js/gtag.js"]', 'data-cookieconsent', 'gtag');

    $session->elementsCount('css', 'script[src*="/js/gtag.ajax.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'type', '');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'data-cookieconsent', 'gtag');

    // @todo For some reason inside the tests, the script isn't present.
    // Although, if we check the HMTL output, it is present.
    // Check that
    // script[src="https://www.googletagmanager.com/gtm.js?id=GTM-xxxxxx"]
    // is present here.
  }

  /**
   * Tests if the cookies ga javascript file is correctly knocked in / out.
   */
  public function testGtagJsCorrectlyKnocked() {
    $session = $this->assertSession();
    // Service consent is required, both javascript files should be disabled
    // and the googletagmanager js shouldn't be displayed at all:
    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="/js/gtag.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.js"]', 'type', 'text/plain');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.js"]', 'data-cookieconsent', 'gtag');

    $session->elementsCount('css', 'script[src*="/js/gtag.ajax.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'type', 'text/plain');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'data-cookieconsent', 'gtag');

    $this->assertNull($session->waitForElement('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]'));
    $session->elementNotExists('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);

    $this->clearBackendCaches();

    // Service consent is not required anymore, both js should be enabled now
    // and the googletagmanager.js should appear:
    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="/js/gtag.js"]', 1);
    $session->elementAttributeNotExists('css', 'script[src*="/js/gtag.js"]', 'type');
    $session->elementAttributeExists('css', 'script[src*="/js/gtag.js"]', 'data-cookieconsent', 'gtag');

    $session->elementsCount('css', 'script[src*="/js/gtag.ajax.js"]', 1);
    $session->elementAttributeNotExists('css', 'script[src*="/js/gtag.ajax.js"]', 'type');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'data-cookieconsent', 'gtag');

    $this->assertNotNull($session->waitForElement('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]'));
    $session->elementExists('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]');
  }

  /**
   * Tests if the js file is correctly knocked in / out with js aggregation on.
   */
  public function testGtagJsCorrectlyKnockedWithJsAggregation() {
    // Test that scripts are knocked out even when JS aggregation is enabled.
    $this->config('system.performance')->set('js.preprocess', TRUE)->save();

    $session = $this->assertSession();
    // Service consent is required, both javascript files should be disabled
    // and the googletagmanager js shouldn't be displayed at all:
    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="/js/gtag.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.js"]', 'type', 'text/plain');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.js"]', 'data-cookieconsent', 'gtag');

    $session->elementsCount('css', 'script[src*="/js/gtag.ajax.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'type', 'text/plain');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'data-cookieconsent', 'gtag');

    $this->assertNull($session->waitForElement('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]'));
    $session->elementNotExists('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);

    $this->clearBackendCaches();

    // Service consent is not required anymore, both js should be enabled now
    // and the googletagmanager.js should appear:
    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="/js/gtag.js"]', 1);
    $session->elementAttributeNotExists('css', 'script[src*="/js/gtag.js"]', 'type');
    $session->elementAttributeExists('css', 'script[src*="/js/gtag.js"]', 'data-cookieconsent', 'gtag');

    $session->elementsCount('css', 'script[src*="/js/gtag.ajax.js"]', 1);
    $session->elementAttributeNotExists('css', 'script[src*="/js/gtag.ajax.js"]', 'type');
    $session->elementAttributeContains('css', 'script[src*="/js/gtag.ajax.js"]', 'data-cookieconsent', 'gtag');

    $this->assertNotNull($session->waitForElement('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]'));
    $session->elementExists('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]');
  }

}
