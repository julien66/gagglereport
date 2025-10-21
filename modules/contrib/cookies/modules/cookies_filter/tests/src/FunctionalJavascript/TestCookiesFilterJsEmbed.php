<?php

namespace Drupal\Tests\cookies_filter\FunctionalJavascript;

use Drupal\cookies_filter\Entity\CookiesServiceFilterEntity;

/**
 * Tests cookies_filter embed Element functionalities.
 *
 * Note that we tests only very basic functionalities, since this element is
 * very close to "iframes", which we test intensively.
 *
 * @group cookies_filter
 */
class TestCookiesFilterJsEmbed extends CookiesFilterJsTestBase {

  /**
   * Tests basic embed blocking functionalities with placeholder "none".
   */
  public function testEmbedBlockingPlaceholderNoneBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');
    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'embed',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'none',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<embed id="testid" class="testClass" src="demo_embed.html"></embed>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the embed gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'embed#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'embed#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-replaced--src');
    // See if it has no placeholder class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the embed is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-replaced--src');
  }

  /**
   * Tests basic embed blocking functionalities with placeholder "hide".
   */
  public function testEmbedBlockingPlaceholderHideBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'embed',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'hide',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<embed id="testid" class="testClass" src="demo_embed.html"></embed>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the embed gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'embed#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'embed#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeContains('css', '#testid', 'class', 'hidden');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');

    // Fire consent script, accept all cookies:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the embed is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-hidden');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'hidden');
  }

  /**
   * Tests basic embed blocking functionalities with placeholder "overlay".
   */
  public function testEmbedBlockingPlaceholderOverlayBasic() {
    $session = $this->assertSession();
    // Place the Cookie UI Block:
    $this->drupalPlaceBlock('cookies_ui_block');

    // Create a cookies_service_filter entity:
    $cookiesFilterEntity = CookiesServiceFilterEntity::create([
      'id' => 'test',
      'label' => 'test',
      'service' => 'functional',
      'elementType' => 'embed',
      'elementSelectors' => '',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<embed id="testid" class="testClass" src="demo_embed.html"></embed>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the embed gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'embed#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', 'embed#testid', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'testClass');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'cookies-filter-replaced--src');
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
    // Go to the node and check if the embed is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'embed#testid', 'class', 'testClass');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeNotContains('css', 'embed#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--functional--wrap > #testid');
  }

}
