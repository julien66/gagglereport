<?php

namespace Drupal\Tests\form_api_example\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Drupal\Core\Url;

/**
 * @group form_api_example
 *
 * @ingroup form_api_example
 */
class AjaxColorFormTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Our module dependencies.
   *
   * @var string[]
   */
  protected static $modules = ['form_api_example'];

  /**
   * Functional test of the color temperature AJAX dropdown form.
   */
  public function testAjaxColorForm() {
    // Visit form route.
    $this->drupalGet(Url::fromRoute('form_api_example.ajax_color_demo'));

    // Get Mink stuff.
    $assert = $this->assertSession();

    // Before the color temperature dropdown is selected, we should not have a
    // color dropdown.
    $assert->fieldNotExists('color');

    $color_matrix = [
      'warm' => ['red', 'orange', 'yellow'],
      'cool' => ['blue', 'purple', 'green'],
    ];

    foreach ($color_matrix as $temperature => $colors) {
      // Submit all the colors.
      foreach ($colors as $color) {
        $assert->selectExists('temperature')->selectOption($temperature);
        $assert->assertWaitOnAjaxRequest();
        $assert->selectExists('color')->selectOption($color);
        $assert->buttonExists('Submit')->press();
        $assert->pageTextContains("Value for Temperature: $temperature");
        $assert->pageTextContains("Value for color: $color");
      }
    }
    $assert->selectExists('temperature')->selectOption('');
    $assert->assertWaitOnAjaxRequest();
    $assert->fieldNotExists('color');
  }

}
