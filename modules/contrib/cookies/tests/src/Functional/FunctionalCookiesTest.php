<?php

namespace Drupal\Tests\cookies\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * This class provides methods for testing the cookies module.
 *
 * @group cookies
 */
class FunctionalCookiesTest extends BrowserTestBase {

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
    // Other module:
    'block',
    'cookies',
    'test_page_test',
    'filter',
  ];

  /**
   * A user with authenticated permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * A admin user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $adminuser;

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
    $this->adminuser = $this->drupalCreateUser([]);
    $this->adminuser->addRole($this->createAdminRole('administrator', 'administrator'));
    $this->adminuser->save();
    $this->user = $this->drupalCreateUser([]);
    $this->drupalLogin($this->adminuser);
  }

  /**
   * Test Access on cookies setting page as authenticated user.
   */
  public function testSettingsPageAccessAsAuth() {
    $this->drupalLogout();
    $this->drupalLogin($this->user);
    $this->drupalGet('/admin/config/system/cookies/config');
    $this->assertSession()->statusCodeEquals('403');
  }

  /**
   * Test Access on cookies setting page as admin user.
   */
  public function testSettingsPageAccessAsAdmin() {
    $this->drupalGet('/admin/config/system/cookies/config');
    $this->assertSession()->statusCodeEquals('200');
  }

  /**
   * Test Access on cookies setting page as anonymous user.
   */
  public function testSettingsPageAccessAsAnonymous() {
    $this->drupalLogout();
    $this->drupalGet('/admin/config/system/cookies/config');
    $this->assertSession()->statusCodeEquals('403');
  }

  /**
   * Tests adding a group by form.
   */
  public function testAddingGroupByForm() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-group/add');
    $session->statusCodeEquals(200);
    $page->fillField('edit-label', 'test123Specific');
    $page->fillField('edit-weight', '50');
    $page->fillField('edit-title', 'myDisplay');
    $page->fillField('edit-details', 'myDetails');
    $page->fillField('edit-id', 'test123');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('Created the test123Specific Cookie service group.');
    $this->drupalGet('/admin/config/system/cookies/cookies-service-group');
    $session->statusCodeEquals(200);
    $session->pageTextContains('test123Specific');
  }

  /**
   * Tests editing a group by form.
   */
  public function testEditingGroupByForm() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-group/video/edit');
    $session->statusCodeEquals(200);
    $page->fillField('edit-label', 'test123Specific');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('Saved the test123Specific Cookie service group.');
    $this->drupalGet('/admin/config/system/cookies/cookies-service-group');
    $session->statusCodeEquals(200);
    $session->pageTextContains('test123Specific');
  }

  /**
   * Tests deleting a group by form.
   */
  public function testDeletingGroupByForm() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-group/performance/delete');
    $session->statusCodeEquals(200);
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('content cookies_service_group: deleted Performance.');
    $this->drupalGet('/admin/config/system/cookies/cookies-service-group');
    $session->statusCodeEquals(200);
    $session->pageTextNotContains('Performance');
  }

  /**
   * Tests adding a service by form.
   */
  public function testAddingServiceByForm() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service/add');
    $session->statusCodeEquals(200);
    $page->fillField('edit-label', 'test123Specific');
    $page->fillField('edit-id', 'test123');
    $page->fillField('edit-group', 'functional');
    $page->fillField('edit-placeholdermaintext', 'My placeholder main text.');
    $page->fillField('edit-placeholderaccepttext', 'My placeholder accept text.');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('Created the test123Specific Cookie service entity.');
    $this->drupalGet('/admin/config/system/cookies/cookies-service');
    $session->statusCodeEquals(200);
    $session->pageTextContains('test123Specific');
  }

  /**
   * Tests editing a service by form.
   */
  public function testEditingServiceByForm() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service/functional/edit');
    $session->statusCodeEquals(200);
    $page->fillField('edit-label', 'test123Specific');
    $page->selectFieldOption('edit-info-format--2', 'plain_text');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('Saved the test123Specific Cookie service entity.');
    $this->drupalGet('/admin/config/system/cookies/cookies-service');
    $session->statusCodeEquals(200);
    $session->pageTextContains('test123Specific');
  }

  /**
   * Tests deleting a service by form.
   */
  public function testDeletingServiceByForm() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service/functional/delete');
    $session->statusCodeEquals(200);
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('content cookies_service: deleted Required functional.');
    $this->drupalGet('/admin/config/system/cookies/cookies-service');
    $session->statusCodeEquals(200);
    $session->pageTextContains('There are no cookies service entities yet.');
  }

}
