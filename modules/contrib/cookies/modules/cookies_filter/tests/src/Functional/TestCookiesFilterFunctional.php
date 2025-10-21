<?php

namespace Drupal\Tests\cookies_filter\Functional;

use Drupal\cookies_filter\Entity\CookiesServiceFilterEntity;
use Drupal\Tests\BrowserTestBase;

/**
 * This class provides methods for testing cookies_filter.
 *
 * @group cookies_filter
 */
class TestCookiesFilterFunctional extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'cookies',
    'cookies_filter',
    'filter_test',
    'block',
  ];

  /**
   * A user with authenticated permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $user;

  /**
   * A user with admin permissions.
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

    $this->user = $this->drupalCreateUser([]);
    $this->adminUser = $this->drupalCreateUser();
    $this->adminUser->addRole($this->createAdminRole('admin', 'admin'));
    $this->adminUser->save();
    $this->drupalLogin($this->adminUser);
    // Create article content type:
    $this->createContentType(['type' => 'article']);
  }

  /**
   * Tests if the CookiesFilter pages exists.
   */
  public function testCookiesFilterUiExists() {
    $session = $this->assertSession();
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelector' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter');
    $session->statusCodeEquals(200);
    $session->pageTextContains('COOKiES service filter entity');
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/add');
    $session->statusCodeEquals(200);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/test/edit');
    $session->statusCodeEquals(200);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/test/delete');
    $session->statusCodeEquals(200);
  }

  /**
   * Tests if an authenticated user doesn't have access to CookiesFilter pages.
   */
  public function testAccessUiAsAuth() {
    $session = $this->assertSession();
    $this->drupalLogout();
    $this->drupalLogin($this->user);
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelector' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter');
    $session->statusCodeEquals(403);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/add');
    $session->statusCodeEquals(403);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/test/edit');
    $session->statusCodeEquals(403);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/test/delete');
    $session->statusCodeEquals(403);
  }

  /**
   * Tests if an anonymous user doesn't have access to CookiesFilter pages.
   */
  public function testAccessUiAsAnonym() {
    $session = $this->assertSession();
    $this->drupalLogout();
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelector' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter');
    $session->statusCodeEquals(403);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/add');
    $session->statusCodeEquals(403);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/test/edit');
    $session->statusCodeEquals(403);
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/test/delete');
    $session->statusCodeEquals(403);
  }

  /**
   * Test the UI for creatung a filter service.
   */
  public function testCreatingFilterService() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/add');
    $session->statusCodeEquals(200);
    $page->fillField('edit-label', 'mySpecificService');
    $page->fillField('edit-id', 'myspecificservice');
    $page->fillField('edit-service', 'functional');
    $page->fillField('edit-elementtype-iframe', 'iframe');
    $page->fillField('edit-placeholderbehaviour', 'none');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('Created the mySpecificService Cookie service filter entity.');
  }

  /**
   * Test creation of a service without the type set will fail.
   */
  public function testSelectorMissingType() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/system/cookies/cookies-service-filter/add');
    $session->statusCodeEquals(200);
    $page->fillField('edit-label', 'mySpecificService');
    $page->fillField('edit-id', 'myspecificservice');
    $page->fillField('edit-service', 'functional');
    $page->fillField('edit-elementtype-iframe', 'iframe');
    $page->fillField('edit-elementselectors', 'div');
    $page->fillField('edit-placeholderbehaviour', 'none');
    $page->pressButton('edit-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('One of the selectors does not contain iframe');
  }

  /**
   * Tests if the cookies format filter exists.
   */
  public function testFormatFilterExists() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/content/formats/manage/filter_test');
    $session->statusCodeEquals(200);
    $page->hasField('edit-filters-cookies-filter-status');
  }

  /**
   * Tests if the cookies format filter exists.
   */
  public function testCreateFormatFilterInUi() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $this->drupalGet('/admin/config/content/formats/manage/filter_test');
    $session->statusCodeEquals(200);
    $page->checkField('edit-filters-cookies-filter-status');
    $page->pressButton('edit-actions-submit');
    $session->statusCodeEquals(200);
    $session->pageTextContains('The text format Test format has been updated.');
  }

}
