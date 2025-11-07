<?php

namespace Drupal\Tests\render_example\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the render example functionality.
 *
 * @group render_example
 * @group examples
 *
 * @ingroup render_example
 */
class RenderExampleTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['render_example'];

  /**
   * {@inheritdoc}
   *
   * Most of our render array altering behaviors are dependent on the standard
   * installation profile, so we'll use that here.
   */
  protected $profile = 'standard';

  /**
   * Functional tests of the pages we made.
   *
   * Note these tests depend on the 'standard' profile.
   */
  public function testRenderExample() {
    // Create a user that can access devel information and log in.
    $web_user = $this->createUser([
      'access content',
    ]);
    $this->drupalLogin($web_user);

    $session = $this->assertSession();

    $altering_url = Url::fromRoute('render_example.altering');

    $this->drupalGet($altering_url);
    // Make sure we're telling the user about devel.
    $session->pageTextContains('Install the Devel module (https://www.drupal.org/project/devel) to enable additional demonstration features.');

    // Test moving the breadcrumb to the top of the content region. Since we
    // just installed render_example and the config defaults to FALSE for all
    // the alter options, we shouldn't have to manage state before making
    // assertions.
    $breadcrumb_xpath = "//main//div[@id='block-stark-breadcrumbs']";
    $this->assertEmpty($this->xpath($breadcrumb_xpath));
    // Move the breadcrumbs to content region.
    $this->drupalGet($altering_url);
    $this->submitForm(['render_example_move_breadcrumbs' => TRUE, 'render_example_reverse_sidebar' => FALSE, 'render_example_wrap_blocks' => FALSE], 'Save configuration');
    $session->pageTextContains('The configuration options have been saved.');
    $this->assertNotEmpty($this->xpath($breadcrumb_xpath));

    // Test reversing order of items in .layout-sidebar-first. Get the node
    // elements under .layout-sidebar-first.
    $breadcrumb_xpath = "//aside[contains(@class,'layout-sidebar-first')]/div/*";
    $elements = $this->xpath($breadcrumb_xpath);
    // There should be 3 elements.
    $this->assertEquals('block-stark-page-title', $elements[0]->getAttribute('id'));
    $this->assertEquals('block-stark-powered', $elements[1]->getAttribute('id'));
    $this->assertEquals('block-stark-syndicate', $elements[2]->getAttribute('id'));

    $this->drupalGet($altering_url);
    $this->submitForm(['render_example_move_breadcrumbs' => FALSE, 'render_example_reverse_sidebar' => TRUE, 'render_example_wrap_blocks' => FALSE], 'Save configuration');
    // Get the elements again.
    $elements = $this->xpath($breadcrumb_xpath);
    // There should be 3 reversed elements.
    $this->assertEquals('block-stark-page-title', $elements[2]->getAttribute('id'));
    $this->assertEquals('block-stark-powered', $elements[1]->getAttribute('id'));
    $this->assertEquals('block-stark-syndicate', $elements[0]->getAttribute('id'));

    // Test wrapping blocks in divs.
    $xpath = "//div[@class='block-prefix']";
    $this->assertEmpty($this->xpath($xpath));
    $this->drupalGet($altering_url);
    $this->submitForm(['render_example_move_breadcrumbs' => FALSE, 'render_example_reverse_sidebar' => FALSE, 'render_example_wrap_blocks' => TRUE], 'Save configuration');
    $this->assertNotEmpty($this->xpath($xpath));

    // Test some rendering facets of the various render examples.
    $this->drupalGet(Url::fromRoute('render_example.arrays'));

    $xpath_array = [
      // @todo Add more of these.
      'foof' => 'Hello ' . $web_user->getAccountName() . ', welcome to the #cache example.',
    ];
    foreach ($xpath_array as $value) {
      $session->pageTextContains($value);
    }
  }

}
