<?php

namespace Drupal\Tests\cookies_filter\FunctionalJavascript;

use Drupal\cookies_filter\Entity\CookiesServiceFilterEntity;

/**
 * Tests cookies_filter embed Element functionalities.
 *
 * @group cookies_filter
 */
class TestCookiesFilterJsIframe extends CookiesFilterJsTestBase {

  /**
   * Tests basic iframe blocking functionalities with placeholder "none".
   */
  public function testIframeBlockingPlaceholderNoneBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');
    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests multiple iframes with placeholder "none".
   */
  public function testIframeBlockingPlaceholderNoneMultiple() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');
    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid3" class="testClass" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());

    // Check first iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check third iframe:
    $session->elementExists('css', 'iframe#testid3');
    $session->elementAttributeNotExists('css', '#testid3', 'src');
    $session->elementAttributeExists('css', 'iframe#testid3', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid3');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframes are unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check first iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // Check second iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // Check third iframe:
    $session->elementExists('css', '#testid3');
    $session->elementAttributeExists('css', '#testid3', 'src');
    $session->elementAttributeNotExists('css', '#testid3', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests the iframe placeholder none, with a set element selector.
   */
  public function testIframeBlockingPlaceholderNoneElementSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blocked',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();
    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="notBlocked" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    // Check all values for blocked iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for not blocked iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeExists('css', 'iframe#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'notBlocked');
    // See if classes don't exist:
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');

    // Check values for not blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'notBlocked');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests the iframe placeholder none, with multiple element selector.
   */
  public function testIframeBlockingPlaceholderNoneMultipleElementSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => "iframe.blocked\niframe.blockedAswell",
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="blockedAswell" src="demo_iframe.html"></iframe>
                    <iframe id="testid3" class="blocked blockedAswell" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');

    // Check all values for first iframe:
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check all values for third blocked iframe:
    $session->elementAttributeNotExists('css', '#testid3', 'src');
    $session->elementAttributeExists('css', 'iframe#testid3', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid3');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');

    // Check values second blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');

    // Check values third blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid3', 'src');
    $session->elementAttributeNotExists('css', '#testid3', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests iframe placeholder none, with a set element selector and two filters.
   */
  public function testIframeBlockingPlaceholderNoneElementSelectorTwoFilterServices() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blocked',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test2',
      'label' => 'test2',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blockedAswell',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe> <iframe id="testid2" class="blockedAswell" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');
    // Check all values for blocked iframe:
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');

    // Check values for other blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests iframe placeh. none, with set element selector and identical filters.
   */
  public function testIframeBlockingPlaceholderNoneIdenticalFilterServices() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test2',
      'label' => 'test2',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" src="demo_iframe.html"></iframe> <iframe id="testid2" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');
    // Check all values for blocked iframe:
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');

    // Check values for other blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests basic iframe blocking functionalities with placeholder "hide".
   */
  public function testIframeBlockingPlaceholderHideBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
  }

  /**
   * Tests mutliple iframes with placeholder "hide".
   */
  public function testIframeBlockingPlaceholderHideMultiple() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid3" class="testClass" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());

    // Check first iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid2', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check third iframe:
    $session->elementExists('css', 'iframe#testid3');
    $session->elementAttributeNotExists('css', '#testid3', 'src');
    $session->elementAttributeExists('css', 'iframe#testid3', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid3', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid3');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframes are unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check first iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
    // Check second iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'hidden');
    // Check third iframe:
    $session->elementExists('css', '#testid3');
    $session->elementAttributeExists('css', '#testid3', 'src');
    $session->elementAttributeNotExists('css', '#testid3', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'hidden');
  }

  /**
   * Tests the iframe placeholder hide, with a set element selector.
   */
  public function testIframeBlockingPlaceholderHideElementSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blocked',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();
    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="notBlocked" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    // Check all values for blocked iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for not blocked iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeExists('css', 'iframe#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'notBlocked');
    // See if classes don't exist:
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');

    // Check values for not blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'notBlocked');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'hidden');
  }

  /**
   * Tests the iframe placeholder hide, with multiple element selector.
   */
  public function testIframeBlockingPlaceholderHideMultipleElementSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => "iframe.blocked\niframe.blockedAswell",
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="blockedAswell" src="demo_iframe.html"></iframe>
                    <iframe id="testid3" class="blocked blockedAswell" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');

    // Check all values for first iframe:
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid2', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check all values for third blocked iframe:
    $session->elementAttributeNotExists('css', '#testid3', 'src');
    $session->elementAttributeExists('css', 'iframe#testid3', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid3', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid3');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');

    // Check values second blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'hidden');

    // Check values third blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid3', 'src');
    $session->elementAttributeNotExists('css', '#testid3', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'hidden');
  }

  /**
   * Tests iframe placeholder hide, with a set element selector and two filters.
   */
  public function testIframeBlockingPlaceholderHideElementSelectorTwoFilterServices() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blocked',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test2',
      'label' => 'test2',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blockedAswell',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe> <iframe id="testid2" class="blockedAswell" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');
    // Check all values for blocked iframe:
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid2', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');

    // Check values for other blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'hidden');
  }

  /**
   * Tests iframe placeh. hide, with set element selector and identical filters.
   */
  public function testIframeBlockingPlaceholderHideIdenticalFilterServices() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test2',
      'label' => 'test2',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" src="demo_iframe.html"></iframe> <iframe id="testid2" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');
    // Check all values for blocked iframe:
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid2', 'class', 'hidden');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check values for blocked iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');

    // Check values for other blocked iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests iframe blocking placeholder "hide" and custom selector.
   */
  public function testIframePlaceholderHideCustomSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderCustomElementSelectors' => 'div.specificDiv',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <div id="testDiv" class="specificDiv"></div>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'div#testDiv');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');

    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check that usual iframe classes are set on custom selector instead:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'hidden');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check element:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
    // Check custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'hidden');
  }

  /**
   * Tests if multiple iframes use the same custom selector as target to hide.
   */
  public function testMultipleIframePlaceholderHideCustomSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderCustomElementSelectors' => 'div.specificDiv',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="testClass" src="demo_iframe.html"></iframe>
                    <div id="testDiv" class="specificDiv"></div>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'div#testDiv');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist for first iframe:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // See if classes exist for second iframe:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check that usual iframe classes are set on custom selector instead:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'hidden');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check first element:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
    // Check second element:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'hidden');
    // Check custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'hidden');
  }

  /**
   * Tests if multiple iframes use the same custom selector as target to hide.
   */
  public function testIframePlaceholderHideOnMultipleCustomSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderCustomElementSelectors' => "div#testDiv\ndiv#testDiv2",
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <div id="testDiv"div>
                    <div id="testDiv2"></div>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'div#testDiv');
    $session->elementExists('css', 'div#testDiv2');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist for first iframe:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check that usual iframe classes are set on custom selector instead:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // Check first div:
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'hidden');
    // Check second div:
    $session->elementAttributeContains('css', '#testDiv2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testDiv2', 'class', 'cookies-filter-custom');
    $session->elementAttributeContains('css', '#testDiv2', 'class', 'hidden');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check element:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
    // Check first custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'hidden');
    // Check second custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv2', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testDiv2', 'class', 'cookies-filter-custom');
    $session->elementAttributeNotContains('css', '#testDiv2', 'class', 'hidden');
  }

  /**
   * Tests basic iframe blocking functionalities with placeholder "overlay".
   */
  public function testIframeBlockingPlaceholderOverlayBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid');
    // See if there is an overlay inside the wrapper:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay');
    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');
  }

  /**
   * Tests mutliple iframes with placeholder "overlay".
   */
  public function testIframeBlockingPlaceholderOverlayMultiple() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid3" class="testClass" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());

    // Check first iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check third iframe:
    $session->elementExists('css', 'iframe#testid3');
    $session->elementAttributeNotExists('css', '#testid3', 'src');
    $session->elementAttributeExists('css', 'iframe#testid3', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid3');

    // See if there are 3 overlay wrappers:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 3);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframes are unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check first iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check third iframe:
    $session->elementExists('css', '#testid3');
    $session->elementAttributeExists('css', '#testid3', 'src');
    $session->elementAttributeNotExists('css', '#testid3', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid3');

    // See if there are no overlay wrappers left:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap', 0);
  }

  /**
   * Tests the iframe placeholder overlay, with a set element selector.
   */
  public function testIframeBlockingPlaceholderOverlayElementSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blocked',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();
    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="notBlocked" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    // Check all values for blocked iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for not blocked iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeExists('css', 'iframe#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'notBlocked');
    // See if classes don't exist:
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check blocked iframe:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check unblocked iframe:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'notBlocked');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');
  }

  /**
   * Tests the iframe placeholder overlay, with multiple element selector.
   */
  public function testIframeBlockingPlaceholderOverlayMultipleElementSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => "iframe.blocked\niframe.blockedAswell",
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="blockedAswell" src="demo_iframe.html"></iframe>
                    <iframe id="testid3" class="blocked blockedAswell" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');

    // Check all values for first iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check all values for third blocked iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid3', 'src');
    $session->elementAttributeExists('css', 'iframe#testid3', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid3');

    // See if there are 3 overlay wrappers:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 3);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
        document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check first iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // Check third iframe:
    $session->elementExists('css', '#testid3');
    $session->elementAttributeExists('css', '#testid3', 'src');
    $session->elementAttributeNotExists('css', '#testid3', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid3', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid3', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid3', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid3');
  }

  /**
   * Tests iframe overlay, with set element selector and two filters.
   */
  public function testIframeBlockingPlaceholderOverlayElementSelectorTwoFilterServices() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blocked',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test2',
      'label' => 'test2',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => 'iframe.blockedAswell',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe> <iframe id="testid2" class="blockedAswell" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');

    // Check all values for first iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // See if there are 2 overlay wrappers:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 2);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check first iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // See if there are no overlay wrappers left:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap', 0);
  }

  /**
   * Tests iframe placeh. overlay, set element selector and identical filters.
   */
  public function testIframeBlockingPlaceholderOverlayIdenticalFilterServices() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test2',
      'label' => 'test2',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" src="demo_iframe.html"></iframe> <iframe id="testid2" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'iframe#testid2');
    // Check all values for blocked iframe:
    // Check all values for first iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check all values for second blocked iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // See if there are 2 overlay wrappers:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap', 2);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check first iframe:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');

    // See if there are no overlay wrappers:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap', 0);
  }

  /**
   * Tests iframe blocking placeholder "overlay" and custom selector.
   */
  public function testIframePlaceholderOverlayCustomSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderCustomElementSelectors' => 'div.specificDiv',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <div id="testDiv" class="specificDiv"></div>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'div#testDiv');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // Check that usual iframe classes and wrappers are set on custom selector
    // instead:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testDiv');
    // See if there is an overlay inside the wrapper:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check element:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // Check custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-custom');

    // See if wrapper is gone:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay');
  }

  /**
   * Tests if multiple iframes use same custom selector as target to overlay.
   */
  public function testMultipleIframePlaceholderOverlayCustomSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderCustomElementSelectors' => 'div.specificDiv',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <iframe id="testid2" class="testClass" src="demo_iframe.html"></iframe>
                    <div id="testDiv" class="specificDiv"></div>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());

    // Check first iframe:
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'div#testDiv');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // Check that usual iframe classes and wrappers are set on custom selector
    // instead:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Check second iframe:
    $session->elementExists('css', 'iframe#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'iframe#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    // Check that usual iframe classes and wrappers are set on custom selector
    // instead:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid2');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testDiv');

    // See if there is only one overlay wrapper:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 1);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check first element:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // Check second element:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid2', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // Check custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-custom');

    // See if there are no overlay wrappers left:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap', 0);
  }

  /**
   * Tests if multiple iframes use same custom selector as target to overlay.
   */
  public function testIframePlaceholderOverlayOnMultipleCustomSelector() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'iframe',
      'elementSelectors' => '',
      'placeholderCustomElementSelectors' => "div#testDiv\ndiv#testDiv2",
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="testClass" src="demo_iframe.html"></iframe>
                    <div id="testDiv"div>
                    <div id="testDiv2"></div>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'div#testDiv');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'iframe#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    // Check that usual iframe classes and wrappers are set on custom selector
    // instead:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');
    // Check first div:
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testDiv');

    // Check second div:
    $session->elementAttributeContains('css', '#testDiv2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeContains('css', '#testDiv2', 'class', 'cookies-filter-custom');
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testDiv2');

    // See if there is only one overlay wrapper:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 2);

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    // Check element:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // Check first custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testDiv', 'class', 'cookies-filter-custom');
    // Check second custom selector element:
    $session->elementAttributeNotContains('css', '#testDiv2', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testDiv2', 'class', 'cookies-filter-custom');

    // See if there are no overlay wrappers left:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap', 0);
  }

}
