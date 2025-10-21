<?php

namespace Drupal\Tests\cookies\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\cookies\Traits\CookiesCacheClearTrait;

/**
 * This class provides methods for testing the cookies module.
 *
 * @group cookies
 */
class FunctionalCookiesStyleTest extends BrowserTestBase {
  use CookiesCacheClearTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    // Requirements for cookies:
    'language',
    'file',
    'field',
    'locale',
    'config_translation',
    // Other modules:
    'block',
    'cookies',
    'test_page_test',
    'filter',
  ];

  /**
   * A admin user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Use the test page as the front page.
    $this->config('system.site')->set('page.front', '/test-page')->save();
    // Disable css aggregation.
    $this->config('system.performance')->set('css.preprocess', FALSE)->save();
    $this->adminUser = $this->drupalCreateUser();
    $this->adminUser->addRole($this->createAdminRole('administrator', 'administrator'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);

    // Create a fake service, so we get the banner displayed.
    $this->config('cookies.cookies_service.fake_service')
      ->set('consentRequired', TRUE)
      ->save();
    // Place Cookie UI block.
    $this->drupalPlaceBlock('cookies_ui_block', [
      'region' => 'content',
      'theme' => $this->defaultTheme,
    ]);
  }

  /**
   * Test style settings.
   */
  public function testStyleSettings() {
    $xpath = $this->assertSession()->buildXPathQuery("//link[contains(@href, :path)]", [':path' => '/libraries/cookiesjsr/dist/cookiesjsr.min.css']);
    $session = $this->assertSession();

    // Check that the defaults are injected.
    $cookies_default = \Drupal::config('cookies.config');
    $this->assertTrue($cookies_default->get('use_default_styles'));

    // Check if the css is loaded.
    $this->drupalGet('<front>');
    $links = $this->getSession()->getPage()->findAll('xpath', $xpath);
    $this->assertNotEmpty($links, 'Default stylesheet not found.');

    // Update the defaults and test them.
    $this->drupalGet('admin/config/system/cookies/config');
    $session->statusCodeEquals(200);
    $values = [
      'use_default_styles' => FALSE,
    ];
    $this->submitForm($values, 'Save configuration');
    $session->pageTextContains('The configuration options have been saved.');
    $this->clearBackendCaches();

    // Check if the default css is not loaded.
    $this->drupalGet('<front>');
    $links = $this->getSession()->getPage()->findAll('xpath', $xpath);
    $this->assertEmpty($links, 'Default stylesheet found.');

    $this->drupalLogout();
  }

}
