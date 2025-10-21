<?php

namespace Drupal\Tests\cookies_matomo\FunctionalJavascript;

use Drupal\cookies\Constants\CookiesConstants;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\cookies\Traits\CookiesCacheClearTrait;

/**
 * Tests cookies_matomo Javascript related functionalities.
 *
 * @group cookies_matomo
 */
class CookiesMatomoFunctionalJavascriptTest extends WebDriverTestBase {
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
    'matomo',
    'cookies',
    'cookies_matomo',
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
    // Set matomo settings:
    $this->config('matomo.settings')->set('site_id', '1')->save();
    $this->config('matomo.settings')->set('url_http', 'https://www.example.com/matomo/')->save();
    $this->config('matomo.settings')->set('url_https', 'https://www.example.com/matomo/')->save();

    $this->clearBackendCaches();
  }

  /**
   * Tests if the cookies ga javascript file is correctly knocked in / out.
   */
  public function testMatomoJsCorrectlyKnocked() {
    $session = $this->assertSession();
    $driver = $this->getSession()->getDriver();

    $this->drupalGet('<front>');
    $session->elementExists('css', 'script#cookies_matomo');
    $session->elementAttributeContains('css', 'script#cookies_matomo', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    // Check if window._paq is undefined:
    $windowPaqUndefined = $driver->evaluateScript('window._paq');
    $this->assertEquals(NULL, $windowPaqUndefined);
    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $driver->executeScript($script);

    $this->clearBackendCaches();

    $this->drupalGet('<front>');
    $session->elementNotExists('css', 'script#cookies_matomo');
    // Check if window._paq is now defined and returns an array:
    $windowPaqDefined = $driver->evaluateScript('window._paq');
    $this->assertIsArray($windowPaqDefined);
    $this->assertNotEmpty($windowPaqDefined);
  }

}
