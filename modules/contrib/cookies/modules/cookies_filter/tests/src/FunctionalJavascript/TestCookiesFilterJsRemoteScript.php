<?php

namespace Drupal\Tests\cookies_filter\FunctionalJavascript;

use Drupal\cookies\Constants\CookiesConstants;
use Drupal\cookies_filter\Entity\CookiesServiceFilterEntity;

/**
 * Tests cookies_filter script Element functionalities.
 *
 * @group cookies_filter
 */
class TestCookiesFilterJsRemoteScript extends CookiesFilterJsTestBase {

  /**
   * Tests basic script blocking functionalities with placeholder "none".
   */
  public function testRemoteScriptSrcBlocked() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');
    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'script',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<script id="testid" class="testClass" src="bla.js" />',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the script gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'script#testid');
    $session->elementAttributeContains('css', 'script#testid', 'type', CookiesConstants::COOKIES_SCRIPT_KO_TYPE);
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', '#testid', 'data-src');

    // See if classes exist:
    $session->elementAttributeContains('css', 'script#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'script#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'script#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'script#testid', 'class', 'cookies-filter-replaced--type');
    $session->elementAttributeContains('css', 'script#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the script is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeContains('css', 'script#testid', 'type', 'text/javascript');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'script#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'script#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'script#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'script#testid', 'class', 'cookies-filter-replaced--type');
    $session->elementAttributeNotContains('css', 'script#testid', 'class', 'cookies-filter-replaced--src');
  }

}
