<?php

namespace Drupal\Tests\cookies_filter\FunctionalJavascript;

use Drupal\cookies_filter\Entity\CookiesServiceFilterEntity;

/**
 * Tests cookies_filter object Element functionalities.
 *
 * Note that we tests only very basic functionalities, since this element is
 * very close to "iframes", which we test intensively. With the main difference,
 * that it uses "data" instead of "data"
 *
 * @group cookies_filter
 */
class TestCookiesFilterJsObject extends CookiesFilterJsTestBase {

  /**
   * Tests basic object blocking functionalities with placeholder "none".
   */
  public function testObjectBlockingPlaceholderNoneBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');
    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'object',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<object id="testid" class="testClass" data="demo_object.html"></object>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the object gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'object#testid');
    $session->elementAttributeNotExists('css', '#testid', 'data');
    $session->elementAttributeExists('css', 'object#testid', 'data-data');
    // See if classes exist:
    $session->elementAttributeContains('css', 'object#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-replaced--data');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the object is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'data');
    $session->elementAttributeNotExists('css', '#testid', 'data-data');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-replaced--data');
  }

  /**
   * Tests basic object blocking functionalities with placeholder "hide".
   */
  public function testObjectBlockingPlaceholderHideBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'object',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<object id="testid" class="testClass" data="demo_object.html"></object>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the object gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'object#testid');
    $session->elementAttributeNotExists('css', '#testid', 'data');
    $session->elementAttributeExists('css', 'object#testid', 'data-data');
    // See if classes exist:
    $session->elementAttributeContains('css', 'object#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-replaced--data');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the object is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'data');
    $session->elementAttributeNotExists('css', '#testid', 'data-data');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-replaced--data');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
  }

  /**
   * Tests basic object blocking functionalities with placeholder "overlay".
   */
  public function testObjectBlockingPlaceholderOverlayBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'object',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<object id="testid" class="testClass" data="demo_object.html"></object>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the object gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'object#testid');
    $session->elementAttributeNotExists('css', '#testid', 'data');
    $session->elementAttributeExists('css', 'object#testid', 'data-data');
    // See if classes exist:
    $session->elementAttributeContains('css', 'object#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'cookies-filter-replaced--data');
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
    // Go to the node and check if the object is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'data');
    $session->elementAttributeNotExists('css', '#testid', 'data-data');
    $session->elementAttributeContains('css', 'object#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'object#testid', 'class', 'cookies-filter-replaced--data');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');
  }

}
