<?php

namespace Drupal\Tests\cookies_asset_injector\FunctionalJavascript;

use Drupal\cookies\Entity\CookiesServiceEntity;
use Drupal\cookies\Entity\CookiesServiceGroup;
use Drupal\Core\Cache\Cache;
use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\cookies\FunctionalJavascript\CookiesFunctionalJsTestBase;
use Drupal\Tests\cookies_asset_injector\Traits\CookiesAssetInjectorTestHelperTrait;
use WebDriver\Exception\UnexpectedAlertOpen;

/**
 * Tests cookies_asset_injector Javascript related functionalities.
 *
 * @group cookies_asset_injector
 */
class TestCookiesAssetInjectorFunctionalJavascript extends CookiesFunctionalJsTestBase {
  use CookiesAssetInjectorTestHelperTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'asset_injector',
    'cookies_asset_injector',
    'system',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Create custom service group:
    CookiesServiceGroup::create([
      'status' => TRUE,
      'id' => 'asset_injector_group',
      'label' => 'Asset Injector Group',
      'weight' => 20,
      'title' => 'Asset Injector Group',
      'details' => 'Testing for Asset Injector Group',
    ])->save();

    // Create COOKiES service with consent required:
    CookiesServiceEntity::create([
      'id' => 'consent_required_service',
      'label' => 'Consent Required Service',
      'group' => 'asset_injector_group',
      'consentRequired' => TRUE,
      'status' => TRUE,
      'info' => [
        'value' => 'Consent Required Service',
        'format' => 'plain_text',
      ],
      'purpose' => '',
      'processor' => '',
      'processorContact' => '',
      'processorUrl' => '',
      'processorPrivacyPolicyUrl' => '',
      'processorCookiePolicyUrl' => '',
      'placeholderMainText' => 'This content is blocked because Asset Injector cookies have not been accepted.',
      'placeholderAcceptText' => 'Only accept Asset Injector cookies',
    ])->save();

    // Create COOKiES service with consent unnecessary:
    CookiesServiceEntity::create([
      'id' => 'consent_unnecessary_service',
      'label' => 'Consent Unnecessary Service',
      'group' => 'asset_injector_group',
      'consentRequired' => FALSE,
      'status' => TRUE,
      'info' => [
        'value' => 'Consent Unnecessary Service',
        'format' => 'plain_text',
      ],
      'purpose' => '',
      'processor' => '',
      'processorContact' => '',
      'processorUrl' => '',
      'processorPrivacyPolicyUrl' => '',
      'processorCookiePolicyUrl' => '',
    ])->save();

    // Cache clear required:
    Cache::invalidateTags([
      'config:cookies.cookies_service',
      'config:cookies.cookies_service_group',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    // Run Grandparent teardown() here, so no unexpected alert
    // exception gets thrown inside "WebDriverTestBase":
    BrowserTestBase::tearDown();
  }

  /**
   * Tests if javascript executes if not assigned to a COOKiES service.
   */
  public function testUnassignedJsNotKnocked() {
    /**
     * @var \Behat\Mink\Driver\Selenium2Driver $driver
     */
    $driver = $this->getSession()->getDriver();

    // Create asset-injector instance:
    $this->createAssetInjector('test_injector', 'Test Injector', 'alert("hello");');

    // Go to front page and expect the alert to fire, as consent is assumed:
    try {
      $this->drupalGet('<front>');
    }
    catch (UnexpectedAlertOpen $e) {
      $this->assertTrue(TRUE);
    }
    $message = $driver->getWebDriverSession()->getAlert_text();
    $driver->getWebDriverSession()->accept_alert();
    $this->assertEquals('hello', $message);
  }

  /**
   * Tests if js won't execute if assigned to consent required COOKiES service.
   */
  public function testAssignedJsKnockedServiceConsentRequired() {
    /**
     * @var \Behat\Mink\Driver\Selenium2Driver $driver
     */
    $driver = $this->getSession()->getDriver();
    // Create asset-injector instance:
    $this->createAssetInjector('test_injector', 'Test Injector', 'alert("hello");', FALSE, FALSE, 'consent_required_service');

    // Go to front page, no alert should be shown:
    $this->drupalGet('<front>');

    // Fire consent script, accept all cookies:
    $script = "document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { all: true }}));";
    $this->getSession()->getDriver()->executeScript($script);
    // Now an alert should be thrown!
    try {
      $this->drupalGet('<front>');
    }
    catch (UnexpectedAlertOpen $e) {
      $this->assertTrue(TRUE);
    }

    $message = $driver->getWebDriverSession()->getAlert_text();
    $driver->getWebDriverSession()->accept_alert();
    $this->assertEquals('hello', $message);
  }

  /**
   * Tests if js executes if assigned to consent unnecessary COOKiES service.
   */
  public function testAssignedJsKnockedServiceConsentUnnecessary() {
    /**
     * @var \Behat\Mink\Driver\Selenium2Driver $driver
     */
    $driver = $this->getSession()->getDriver();
    // Create asset-injector instance:
    $this->createAssetInjector('test_injector', 'Test Injector', 'alert("hello");', FALSE, FALSE, 'consent_unnecessary_service');
    // Go to front page and expect the alert to fire, as consent is assumed:
    try {
      $this->drupalGet('<front>');
    }
    catch (UnexpectedAlertOpen $e) {
      $this->assertTrue(TRUE);
    }
    $message = $driver->getWebDriverSession()->getAlert_text();
    $driver->getWebDriverSession()->accept_alert();
    $this->assertEquals('hello', $message);
  }

  /**
   * Tests if js won't execute if assigned to consent required COOKiES service.
   */
  public function testAssignedJsKnockedServiceConsentRequiredHeaderTrue() {
    /**
     * @var \Behat\Mink\Driver\Selenium2Driver $driver
     */
    $driver = $this->getSession()->getDriver();
    // Create asset-injector instance:
    $this->createAssetInjector('test_injector', 'Test Injector', 'alert("hello");', TRUE, FALSE, 'consent_required_service');

    // Go to front page, no alert should be shown:
    $this->drupalGet('<front>');

    // Fire consent script, accept all cookies:
    $script = "document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { all: true }}));";
    $this->getSession()->getDriver()->executeScript($script);
    // Now an alert should be thrown!
    try {
      $this->drupalGet('<front>');
    }
    catch (UnexpectedAlertOpen $e) {
      $this->assertTrue(TRUE);
    }

    $message = $driver->getWebDriverSession()->getAlert_text();
    $driver->getWebDriverSession()->accept_alert();
    $this->assertEquals('hello', $message);
  }

  /**
   * Tests if js won't execute if assigned to consent required COOKiES service.
   */
  public function testAssignedJsKnockedServiceConsentRequiredAllTrue() {
    /**
     * @var \Behat\Mink\Driver\Selenium2Driver $driver
     */
    $driver = $this->getSession()->getDriver();

    // We need to enable js preprocessing, before we can "preprocess" inside the
    // asset injector:
    $this->config('system.performance')->set('js.preprocess', TRUE)->save();
    // Create asset-injector instance:
    $this->createAssetInjector('test_injector', 'Test Injector', 'alert("hello");', TRUE, TRUE, 'consent_required_service');

    // Go to front page, no alert should be shown:
    $this->drupalGet('<front>');

    // Fire consent script, accept all cookies:
    $script = "document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { all: true }}));";
    // // Now an alert should be thrown!
    $this->getSession()->getDriver()->executeScript($script);
    try {
      $this->drupalGet('<front>');
    }
    catch (UnexpectedAlertOpen $e) {
      $this->assertTrue(TRUE);
    }

    $message = $driver->getWebDriverSession()->getAlert_text();
    $driver->getWebDriverSession()->accept_alert();
    $this->assertEquals('hello', $message);
  }

}
