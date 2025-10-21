<?php

namespace Drupal\Tests\cookies_filter\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Tests cookies_filter Javascript related functionalities.
 */
abstract class CookiesFilterJsTestBase extends WebDriverTestBase {

  /**
   * An admin user with all permissions.
   *
   * @var \Drupal\user\Entity\User
   */
  protected $adminUser;

  /**
   * User without 'administer_webform_confirmation_javascript' permission.
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
    'cookies',
    'cookies_filter',
    'filter_test',
    'block',
  ];

  /**
   * {@inheritDoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->createContentType(['type' => 'article']);
    $this->createAdminRole('administrator', 'administrator');
    $this->adminUser = $this->createUser([]);
    $this->adminUser->addRole('administrator');
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
    // Edit our test filter format with our custom filter enabled:
    $edit = [
      'edit-filters-cookies-filter-status' => 1,
      'edit-filters-filter-html-escape-status' => 0,
    ];
    $this->drupalGet('admin/config/content/formats/manage/filter_test');
    $this->submitForm($edit, 'Save configuration');
  }

}
