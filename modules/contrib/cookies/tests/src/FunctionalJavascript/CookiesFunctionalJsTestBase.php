<?php

namespace Drupal\Tests\cookies\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Supplies basic Cookies JS Test setup.
 *
 * @group cookies
 */
abstract class CookiesFunctionalJsTestBase extends WebDriverTestBase {

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
    'node',
    'block',
    'cookies',
    'test_page_test',
  ];

  /**
   * Default theme.
   *
   * @var string
   */
  protected $defaultTheme = 'stark';

  /**
   * A test administrator.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * A regular authenticated user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Use the test page as the front page.
    $this->config('system.site')->set('page.front', '/test-page')->save();
    // Enable cookies from cdn for testing:
    $this->config('cookies.config')->set('lib_load_from_cdn', TRUE)->save();
    // Create users:
    $this->user = $this->drupalCreateUser();
    $this->adminUser = $this->drupalCreateUser();
    $this->adminUser->addRole($this->createAdminRole('administrator', 'administrator'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
    // Place the Cookie UI Block:
    $this->placeBlock('cookies_ui_block');
  }

}
