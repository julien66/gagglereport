<?php

namespace Drupal\Tests\cookies_ga\FunctionalJavascript;

use Drupal\cookies\Constants\CookiesConstants;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\cookies\Traits\CookiesCacheClearTrait;

/**
 * Tests cookies_ga Javascript related functionalities.
 *
 * @group cookies_ga
 */
class TestCookiesGaFunctionalJavascript extends WebDriverTestBase {
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
    'google_analytics',
    'cookies',
    'cookies_ga',
  ];

  /**
   * {@inheritDoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->config('system.site')->set('page.front', '/test-page')->save();
    $this->user = $this->drupalCreateUser([]);
    $this->adminUser = $this->drupalCreateUser([]);
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
    $this->drupalPlaceBlock('cookies_ui_block');
    // Set google_analytics settings:
    $this->config('google_analytics.settings')->set('account', 'G-xxxxxxxx')->save();
    $this->config('google_analytics.settings')->set('visibility.request_path_pages', '')->save();
    // Test that scripts are knocked out even when JS aggregation is enabled.
    $this->config('system.performance')->set('js.preprocess', TRUE)->save();
    $this->clearBackendCaches();
  }

  /**
   * Tests if the cookies ga javascript file is correctly knocked in / out.
   */
  public function testGoogleAnalyticsJsCorrectlyKnocked() {
    $session = $this->assertSession();

    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="js/google_analytics.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="js/google_analytics.js"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeContains('css', 'script[src*="js/google_analytics.js"]', 'data-cookieconsent', 'analytics');

    $session->elementsCount('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 1);
    $session->elementAttributeContains('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeContains('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'data-cookieconsent', 'analytics');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);

    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="js/google_analytics.js"]', 1);
    $session->elementAttributeNotExists('css', 'script[src*="js/google_analytics.js"]', 'type');
    $session->elementAttributeContains('css', 'script[src*="js/google_analytics.js"]', 'data-cookieconsent', 'analytics');

    $session->elementsCount('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 1);
    $session->elementAttributeNotExists('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'type');
    $session->elementAttributeContains('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'data-cookieconsent', 'analytics');
  }

  /**
   * Tests if the js file is correctly knocked in / out with js aggregation on.
   */
  public function testGoogleAnalyticsJsCorrectlyKnockedWithJsAggregation() {
    // Test that scripts are knocked out even when JS aggregation is enabled.
    $this->config('system.performance')->set('js.preprocess', TRUE)->save();

    $session = $this->assertSession();

    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="js/google_analytics.js"]', 1);
    $session->elementAttributeContains('css', 'script[src*="js/google_analytics.js"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeContains('css', 'script[src*="js/google_analytics.js"]', 'data-cookieconsent', 'analytics');

    $session->elementsCount('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 1);
    $session->elementAttributeContains('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeContains('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'data-cookieconsent', 'analytics');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);

    $this->drupalGet('<front>');

    $session->elementsCount('css', 'script[src*="js/google_analytics.js"]', 1);
    $session->elementAttributeNotExists('css', 'script[src*="js/google_analytics.js"]', 'type');
    $session->elementAttributeContains('css', 'script[src*="js/google_analytics.js"]', 'data-cookieconsent', 'analytics');

    $session->elementsCount('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 1);
    $session->elementAttributeNotExists('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'type');
    $session->elementAttributeContains('css', 'script[src="https://www.googletagmanager.com/gtag/js?id=G-xxxxxxxx"]', 'data-cookieconsent', 'analytics');
  }

}
