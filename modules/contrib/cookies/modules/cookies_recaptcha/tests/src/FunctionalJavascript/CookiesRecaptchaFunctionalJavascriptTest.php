<?php

namespace Drupal\Tests\cookies_recaptcha\FunctionalJavascript;

use Drupal\cookies\Constants\CookiesConstants;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests cookies_recaptcha Javascript related functionalities.
 *
 * @group cookies_recaptcha
 */
class CookiesRecaptchaFunctionalJavascriptTest extends WebDriverTestBase {

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
    'captcha',
    'recaptcha',
    'cookies',
    'cookies_recaptcha',
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
    // Set captcha and recaptcha settings:
    $this->config('captcha.settings')->set('default_challenge', 'cookies_recaptcha/reCAPTCHA')->save();
    $this->config('recaptcha.settings')->set('site_key', '0000000000000000000000000000000000000000')->save();
    $this->config('recaptcha.settings')->set('secret_key', '0000000000000000000000000000000000000000')->save();
    // Fluch caches, otherwise the script will not show up:
  }

  /**
   * Tests if the cookies ga javascript file is correctly knocked in / out.
   */
  public function testRecaptchaJsCorrectlyKnocked() {
    $session = $this->assertSession();
    $driver = $this->getSession()->getDriver();
    // Enable login captcha point:
    $captcha_point = \Drupal::entityTypeManager()
      ->getStorage('captcha_point')
      ->load('user_login_form');
    $captcha_point->enable()->save();
    $this->drupalLogout();

    // Got to login page and check blocked recaptcha:
    $this->drupalGet('/user/login');
    // There should be two cookies recaptcha scripts:
    $session->elementsCount('css', 'script[id^="cookies_recaptcha_"]', 2);
    // Check, that the recaptcha.ja and google recaptcha api.js are blocked:
    $session->elementAttributeContains('css', 'script[src*="/js/recaptcha.js"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeContains('css', 'script[src^="https://www.google.com/recaptcha/api.js"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $driver->executeScript($script);

    // Revisit the site after accepting all cookies:
    $this->drupalGet('/user/login');
    // The recaptcha scripts should be unblocked now:
    $session->elementExists('css', 'script[src^="https://www.google.com/recaptcha/api.js"]');
    $session->elementAttributeNotExists('css', 'script[src^="https://www.google.com/recaptcha/api.js"]', 'type');

    $session->elementExists('css', 'script[src*="/js/recaptcha.js"]');
    $session->elementAttributeNotExists('css', 'script[src*="/js/recaptcha.js"]', 'type');
  }

}
