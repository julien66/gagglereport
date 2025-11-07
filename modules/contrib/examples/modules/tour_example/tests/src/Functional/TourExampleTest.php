<?php

namespace Drupal\Tests\tour_example\Functional;

use Drupal\Core\Url;
use Drupal\Tests\tour\Functional\TourTestBasic;

/**
 * Regression tests for the tour_example module.
 *
 * We use TourTestBasic to get some built-in tour tip testing assertions.
 *
 * @ingroup tour_example
 *
 * @group tour_example
 * @group examples
 */
class TourExampleTest extends TourTestBasic {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['tour_example'];

  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * {@inheritdoc}
   */
  protected function setUp() : void {
    $this->defaultTheme = 'stark';
    parent::setUp();
  }

  /**
   * Main test.
   *
   * Make sure the Tour Example link is on the front page. Make sure all the
   * tour tips exist on the page. Make sure all the corresponding target
   * elements exist for tour tips that have targets.
   */
  public function testTourExample() {
    $assert = $this->assertSession();

    // Create a user with the permissions we need in order to display the
    // toolbar and run a tour from it.
    $this->drupalLogin($this->createUser([
      'access content',
      'access toolbar',
      'access tour',
    ]));

    // Test for a link to the tour_example in the Tools menu.
    $this->drupalGet(Url::fromRoute('<front>'));
    $assert->statusCodeEquals(200);
    $assert->linkByHrefExists('examples/tour-example');

    // Verify anonymous user can successfully access the tour_examples page.
    $this->drupalGet(Url::fromRoute('tour_example.description'));
    $assert->statusCodeEquals(200);

    // Get all the tour elements. These are the IDs of each tour tip. See them
    // in config/install/tour.tour.tour-example.yml.
    $tip_ids = [
      'introduction' => '',
      'first-item' => '#tour-target-1',
      'second-item' => '#tour-target-2',
      'third-item' => '#tour-target-3',
      'fourth-item' => '#tour-target-4',
    ];

    // Ensure that we have the right number of buttons.
    // First tip does not have accompanying button, so we have less buttons
    // than tour items.
    $this->assertCount(count($tip_ids) - 1, $this->cssSelect('#button-container .button'));

    // Ensure each item exists.
    foreach ($tip_ids as $tip_id => $tip_selector) {
      if (!$tip_selector) {
        continue;
      }
      $this->assertNotEmpty($this->cssSelect($tip_selector),
        "Tip id: $tip_id $tip_selector"
      );
    }

    // Verify that existing tour tips have corresponding target page elements.
    $this->assertTourTips();
  }

}
