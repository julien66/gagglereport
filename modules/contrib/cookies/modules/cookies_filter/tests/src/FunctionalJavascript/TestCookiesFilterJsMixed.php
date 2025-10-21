<?php

namespace Drupal\Tests\cookies_filter\FunctionalJavascript;

use Drupal\cookies_filter\Entity\CookiesServiceFilterEntity;

/**
 * Tests cookies_filter mixed element and services behaviour.
 *
 * @group cookies_filter
 */
class TestCookiesFilterJsMixed extends CookiesFilterJsTestBase {

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
    // Install cookies_ga, so we have an additional filter service, without
    // much setup:
    'cookies_video',
  ];

  /**
   * Tests iframe overlay, with set element selector and two filters.
   */
  public function testDifferentServicesCorrectBlockingOverlay() {
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
      'service' => 'video',
      'elementType' => 'embed',
      'elementSelectors' => 'embed.blockedAswell',
      'placeholderBehaviour' => 'overlay',
    ]);
    $cookiesFilterEntity->save();

    // Create a node:
    $node = $this->createNode([
      'type' => 'article',
      'title' => 'test123',
      'body' => [
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>
                    <embed id="testid2" class="blockedAswell" src="demo_embed.html"></embed>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');
    $session->elementExists('css', 'embed#testid2');

    // Check all values for iframe:
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

    // Check all values for embed:
    $session->elementExists('css', 'embed#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', 'embed#testid2', 'data-src');
    // See if classes exist:
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'cookies-filter-service--video');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if it has no placeholder overlay class set:
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-hidden');
    // See if there is a wrapper set:
    $session->elementExists('css', 'div.cookies-fallback--video--wrap > #testid2');

    // See if there is one video and one functional wrapper:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 1);
    $session->elementsCount('css', 'div.cookies-fallback--video--wrap > div.cookies-fallback.cookies-fallback--video.cookies-fallback--video--overlay', 1);

    // Only consent to functional:
    $script = "document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { services: { functional: true }} }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check iframe unblocked:
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

    // Check embed still blocked:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeNotExists('css', '#testid2', 'src');
    $session->elementAttributeExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'blockedAswell');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'cookies-filter-service--video');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is still a wrapper:
    $session->elementExists('css', 'div.cookies-fallback--video--wrap > #testid2');

    // See if there is only one video and no functional wrapper:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 0);
    $session->elementsCount('css', 'div.cookies-fallback--video--wrap > div.cookies-fallback.cookies-fallback--video.cookies-fallback--video--overlay', 1);

    // Now also consent to video:
    $script = "document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { services: { video: true }} }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check iframe still unblocked:
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

    // Check embed now also unblocked:
    $session->elementExists('css', '#testid2');
    $session->elementAttributeExists('css', '#testid2', 'src');
    $session->elementAttributeNotExists('css', '#testid2', 'data-src');
    $session->elementAttributeContains('css', 'embed#testid2', 'class', 'blockedAswell');
    $session->elementAttributeNotContains('css', 'embed#testid2', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'embed#testid2', 'class', 'cookies-filter-service--video');
    $session->elementAttributeNotContains('css', 'embed#testid2', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid2', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper:
    $session->elementNotExists('css', 'div.cookies-fallback--video--wrap > #testid2');

    // See if there are no wrappers left:
    $session->elementsCount('css', 'div.cookies-fallback--functional--wrap > div.cookies-fallback.cookies-fallback--functional.cookies-fallback--functional--overlay', 0);
    $session->elementsCount('css', 'div.cookies-fallback--video--wrap > div.cookies-fallback.cookies-fallback--video.cookies-fallback--video--overlay', 0);
  }

  /**
   * Tests iframe overlay, with set element selector and two filters.
   */
  public function testOptingOutAfterOptin() {
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
        'value' => '<iframe id="testid" class="blocked" src="demo_iframe.html"></iframe>',
        'format' => 'filter_test',
      ],
    ]);
    // Go to the created node and check if the iframe gets knocked out:
    $this->drupalGet('/node/' . $node->id());
    $session->elementExists('css', 'iframe#testid');

    // Check all values for  iframe:
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

    // Consent to all:
    $script = "var options = { all: true };
      document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check embed unblocked:
    $session->elementExists('css', '#testid');
    $session->elementAttributeExists('css', '#testid', 'src');
    $session->elementAttributeNotExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-service--video');
    $session->elementAttributeNotContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeNotContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is no wrapper anymore:
    $session->elementNotExists('css', 'div.cookies-fallback--video--wrap > #testid');

    // Now opt out again:
    $script = "var options = { all: false };
     document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: options }));";
    // Go to the node and check if the iframe is unblocked:
    $this->getSession()->getDriver()->executeScript($script);
    $this->drupalGet('/node/' . $node->id());

    // Check iframe is blocked again:
    $session->elementExists('css', '#testid');
    $session->elementAttributeNotExists('css', '#testid', 'src');
    $session->elementAttributeExists('css', '#testid', 'data-src');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'blocked');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-processed');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-service--functional');
    $session->elementAttributeContains('css', 'iframe#testid', 'class', 'cookies-filter-replaced--src');
    $session->elementAttributeContains('css', '#testid', 'class', 'cookies-filter-placeholder-type-overlay');
    // See if there is a wrapper again:
    $session->elementExists('css', 'div.cookies-fallback--functional--wrap > #testid');
  }

}
