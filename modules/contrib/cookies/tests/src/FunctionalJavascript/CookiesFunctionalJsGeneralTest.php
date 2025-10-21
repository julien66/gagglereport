<?php

namespace Drupal\Tests\cookies\FunctionalJavascript;

use Drupal\cookies\Entity\CookiesServiceEntity;
use Drupal\cookies\Entity\CookiesServiceGroup;

/**
 * Class for testing simple cookies tests.
 *
 * @group cookies
 */
class CookiesFunctionalJsGeneralTest extends CookiesFunctionalJsTestBase {

  /**
   * Tests to see if the cdn script exists.
   */
  public function testScriptCdnExists() {
    // Enable cookies from cdn:
    $this->config('cookies.config')->set('lib_load_from_cdn', TRUE)->save();
    // Place the Cookie UI Block:
    $this->placeBlock('cookies_ui_block');
    // Check script type before consent:
    $this->drupalGet('<front>');
    $this->assertSession()->elementExists('css', 'script[src*="https://cdn.jsdelivr.net/gh/jfeltkamp/cookiesjsr@1/dist/cookiesjsr.min.js"]');
    $this->assertSession()->elementExists('css', 'script[src*="https://cdn.jsdelivr.net/gh/jfeltkamp/cookiesjsr@1/dist/cookiesjsr-preloader.min.js"]');
  }

  /**
   * Tests to see if the cookiesjsr cookie is set.
   */
  public function testCookieIsSet() {
    // Enable cookies from cdn:
    $this->config('cookies.config')->set('lib_load_from_cdn', TRUE)->save();
    // Place the Cookie UI Block:
    $this->placeBlock('cookies_ui_block');
    // Check script type before consent:
    $this->drupalGet('<front>');
    // Fire consent script:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    $this->getSession()->getDriver()->executeScript($script);
    $cookie = $this->getSession()->getDriver()->getCookie('cookiesjsr');
    $this->assertEquals($cookie, '{"functional":true}');
  }

  /**
   * Tests the cookies banner.
   */
  public function testCookiesBanner() {
    $session = $this->assertSession();
    // Get editable config:
    $cookiesTexts = $this->config('cookies.texts');
    // Set random cookies.texts config values, to see if it has any effect on
    // the Banner:
    $cookiesTexts
      ->set('bannerText', $this->randomString())
      ->set('cookieDocs', $this->randomString())
      ->set('settings', $this->randomString())
      ->set('denyAll', $this->randomString())
      ->set('acceptAll', $this->randomString())
      ->save();
    \Drupal::service('cache_tags.invalidator')->invalidateTags(['config:cookies.texts']);

    $this->drupalPlaceBlock('cookies_ui_block');
    $this->drupalGet('<front>');
    $session->elementExists('css', '#cookiesjsr');
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner');
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--info');
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--action');
    // Check if the default info text is set:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--info > span.cookiesjsr-banner--text');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--info > span.cookiesjsr-banner--text', $cookiesTexts->get('bannerText'));
    // Checke if the cookieDocs string is used:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--info > ul.cookiesjsr-banner--links > li > a');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--info > ul.cookiesjsr-banner--links > li > a', $cookiesTexts->get('cookieDocs'));
    // Check if the settings button exists and the correct text is used:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--action > button.cookiesjsr-btn.cookiesjsr-settings');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--action > button.cookiesjsr-btn.cookiesjsr-settings', strtoupper($cookiesTexts->get('settings')));
    // Check if the "denyAll" button exists and the correct text is used:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--action > button.cookiesjsr-btn.denyAll');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--action > button.cookiesjsr-btn.denyAll', strtoupper($cookiesTexts->get('denyAll')));
    // Check if the "acceptAll" button exists and the correct text is used:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--action > button.cookiesjsr-btn.allowAll');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-banner > div.cookiesjsr-banner--action > button.cookiesjsr-btn.allowAll', strtoupper($cookiesTexts->get('acceptAll')));
  }

  /**
   * Tests the cookies settings banner.
   */
  public function testCookiesSettingsBanner() {
    $session = $this->assertSession();
    $page = $this->getSession()->getPage();
    $cookiesTexts = $this->config('cookies.texts');
    $cookiesConfig = $this->config('cookies.config');
    // Set random settings hash:
    $cookiesConfig->set('open_settings_hash', $this->randomString())->save();
    // Set random cookies.texts config values, to see if it has any effect on
    // the settings banner:
    $cookiesTexts
      ->set('cookieSettings', $this->randomString())
      ->set('settingsAllServices', $this->randomString())
      ->set('denyAll', $this->randomString())
      ->set('acceptAll', $this->randomString())
      ->set('saveSettings', $this->randomString())
      ->save();
    \Drupal::service('cache_tags.invalidator')->invalidateTags(['config:cookies.texts']);
    // Create another service group and service entity, so we have more cookies
    // settings displayed in our settings banner:
    CookiesServiceGroup::create([
      'id' => 'test_group',
      'label' => 'Test Group',
      'status' => TRUE,
      'weight' => 30,
      'title' => 'Test Group',
      'details' => 'Test Group Details',
    ])->save();
    CookiesServiceEntity::create([
      'status' => TRUE,
      'id' => 'test_service_entity',
      'label' => 'Test Service Entity',
      'consentRequired' => TRUE,
      'group' => 'test_group',
      'info' => [
        'value' => 'Test 123',
        'format' => 'plain_text',
      ],
      'weight' => 20,
      'title' => 'Asset Injector Group',
      'details' => 'Testing for Asset Injector Group',
      'purpose' => '',
      'processor' => '',
      'processorContact' => '',
      'processorUrl' => 'https://test.com',
      'processorPrivacyPolicyUrl' => '',
      'processorCookiePolicyUrl' => 'https://test.com/cookies',
      'placeholderMainText' => 'This content is blocked because Functional cookies have not been accepted.',
      'placeholderAcceptText' => 'Only accept Functional cookies',
    ])->save();
    $this->drupalPlaceBlock('cookies_ui_block');
    $this->drupalGet('<front>');
    $this->assertNotNull($session->waitForElementVisible('css', 'div#cookiesjsr button.cookiesjsr-btn.cookiesjsr-settings'));
    $page->pressButton('Cookie settings');

    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-layer--wrapper > div.cookiesjsr-layer');

    // See if header exists and it contains the correct values:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-layer--wrapper > div.cookiesjsr-layer > header.cookiesjsr-layer--header');
    $session->elementExists('css', '#cookiesjsrLabel');
    $session->elementTextEquals('css', '#cookiesjsrLabel', strtoupper($cookiesTexts->get('cookieSettings')));

    // Check if the body exists and contains the correct values:
    // Check all available groups:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-layer--wrapper > div.cookiesjsr-layer > div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups');
    // Should be two groups (functional and test_group):
    $session->elementsCount('css', '#cookiesjsr > div.cookiesjsr--app > div.cookiesjsr-layer--wrapper > div.cookiesjsr-layer > div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups > li.cookiesjsr-service-group', 2);

    // Check our test group values:
    // @todo The id shouldn't be generated with an "_" but instead replaced with a "-"
    $session->elementExists('css', '#tab-test_group');
    $session->elementTextEquals('css', '#tab-test_group', 'Test Group');
    $session->elementExists('css', 'div#panel-test_group');

    // Check our test service values:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service');
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service > div.cookiesjsr-service--description');
    // Check if header is set:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service > div.cookiesjsr-service--description > h3#desc_test_service_entity');

    // Check if remote links are set:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service > div.cookiesjsr-service--description > ul.cookiesjsr-links');
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service > div.cookiesjsr-service--description > ul.cookiesjsr-links > li:nth-child(1) > a[href="https://test.com"]');

    // @codingStandardsIgnoreStart
    // @todo Implement this, once https://www.drupal.org/project/cookies/issues/3326058 is fixed:
    // $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service > div.cookiesjsr-service--description > ul.cookiesjsr-links > li:nth-child(2) > a[href="https://test.com/cookies"]');
    // @codingStandardsIgnoreEnd
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service > div.cookiesjsr-service--description > ul.cookiesjsr-links > li:nth-child(2) > a');
    // Check if checkbox exists:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app div.cookiesjsr-layer--body > ul.cookiesjsr-service-groups li.cookiesjsr-service > div.cookiesjsr-service--action > label.cookiesjsr-switch > input');

    // See if footer exists and contains the correct values:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer');
    // Check if settingsAllServices is set:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--label-all');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--label-all', $cookiesTexts->get('settingsAllServices'));
    // Check if buttons have the correct values:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--actions');
    // Deny button:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--actions > button.cookiesjsr-btn.denyAll');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--actions > button.cookiesjsr-btn.denyAll', strtoupper($cookiesTexts->get('denyAll')));
    // Accept button:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--actions > button.cookiesjsr-btn.allowAll');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--actions > button.cookiesjsr-btn.allowAll', strtoupper($cookiesTexts->get('acceptAll')));
    // Save button:
    $session->elementExists('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--actions > button.cookiesjsr-btn.save');
    $session->elementTextEquals('css', '#cookiesjsr > div.cookiesjsr--app footer.cookiesjsr-layer--footer > div.cookiesjsr-layer--actions > button.cookiesjsr-btn.save', strtoupper($cookiesTexts->get('saveSettings')));
  }

  /**
   * Tests the cookies documentation banner.
   */
  public function todoTestCookiesDocumentationBanner() {

  }

}
