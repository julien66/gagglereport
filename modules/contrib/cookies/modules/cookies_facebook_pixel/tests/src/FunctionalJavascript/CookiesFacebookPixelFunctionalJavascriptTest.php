<?php

namespace Drupal\Tests\cookies_facebook_pixel\FunctionalJavascript;

use Drupal\cookies\Constants\CookiesConstants;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Tests\cookies\Traits\CookiesCacheClearTrait;

/**
 * Tests cookies_facebook_pixel Javascript related functionalities.
 *
 * @group cookies_facebook_pixel
 */
class CookiesFacebookPixelFunctionalJavascriptTest extends WebDriverTestBase {
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
    'node',
    'test_page_test',
    'filter_test',
    'cookies',
    'block',
    'facebook_pixel',
    'cookies_facebook_pixel',
  ];

  /**
   * {@inheritDoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->createContentType(['type' => 'article']);
    $this->config('system.site')->set('page.front', '/test-page')->save();
    $this->user = $this->drupalCreateUser([]);
    $this->adminUser = $this->drupalCreateUser([]);
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
    $this->drupalPlaceBlock('cookies_ui_block');
    // Set facebook_pixel settings:
    $edit = [
      'edit-facebook-pixel-visibility-request-path-pages' => '',
      'edit-facebook-id' => '1234567',
    ];
    $this->drupalGet('admin/config/facebook_pixel');
    $this->submitForm($edit, 'Save configuration');
  }

  /**
   * Tests if the facebook pixel javascript file is correctly knocked in / out.
   */
  public function testFacebookPixelJsCorrectlyKnocked() {
    $session = $this->assertSession();
    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => 'TEST123',
        'format' => 'filter_test',
      ],
    ]);

    $this->drupalGet('/node/' . $node->id());
    // Consent denied, expected result:
    // @codingStandardsIgnoreStart
    // <script src="/modules/custom/facebook_pixel/js/facebook_pixel.js?v=XXXXXXX" type="CookiesConstants::COOKIES_SCRIPT_KO_TYPE" id="facebook_tracking_pixel_script"></script>.
    // @codingStandardsIgnoreEnd
    $session->elementExists('css', 'script#facebook_tracking_pixel_script');
    $session->elementAttributeContains('css', 'script#facebook_tracking_pixel_script', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeContains('css', 'script[src*="facebook_pixel.js"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);

    $this->clearBackendCaches();

    $this->drupalGet('/node/' . $node->id());
    // Since the id is removed on opt in, we have to look for our script like
    // this:
    // Consent given, expected result:
    // @codingStandardsIgnoreStart
    // <script src="/modules/custom/facebook_pixel/js/facebook_pixel.js?v=XXXXXXX"></script>
    // @codingStandardsIgnoreEnd
    $session->elementNotExists('css', 'script#facebook_tracking_pixel_script');
    $session->elementExists('css', 'script[src*="facebook_pixel.js"]');
    $session->elementAttributeNotExists('css', 'script[src*="facebook_pixel.js"]', 'type');
  }

  /**
   * Tests if the js file is correctly knocked in / out, with js aggregation on.
   */
  public function testFacebookPixelJsCorrectlyKnockedWithJsAggregation() {
    // Test that scripts are knocked out even when JS aggregation is enabled.
    $this->config('system.performance')->set('js.preprocess', TRUE)->save();
    $session = $this->assertSession();
    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => 'TEST123',
        'format' => 'filter_test',
      ],
    ]);

    $this->drupalGet('/node/' . $node->id());
    // Consent denied, expected result:
    // @codingStandardsIgnoreStart
    // <script src="/modules/custom/facebook_pixel/js/facebook_pixel.js?v=XXXXXXX" type="CookiesConstants::COOKIES_SCRIPT_KO_TYPE" id="facebook_tracking_pixel_script"></script>.
    // @codingStandardsIgnoreEnd
    $session->elementExists('css', 'script#facebook_tracking_pixel_script');
    $session->elementAttributeContains('css', 'script#facebook_tracking_pixel_script', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeContains('css', 'script[src*="facebook_pixel.js"]', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);

    $this->clearBackendCaches();

    $this->drupalGet('/node/' . $node->id());
    // Since the id is removed on opt in, we have to look for our script like
    // this:
    // Consent given, expected result:
    // @codingStandardsIgnoreStart
    // <script src="/modules/custom/facebook_pixel/js/facebook_pixel.js?v=XXXXXXX"></script>
    // @codingStandardsIgnoreEnd
    $session->elementNotExists('css', 'script#facebook_tracking_pixel_script');
    $session->elementExists('css', 'script[src*="facebook_pixel.js"]');
    $session->elementAttributeNotExists('css', 'script[src*="facebook_pixel.js"]', 'type');
  }

}
