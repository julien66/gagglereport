<?php

namespace Drupal\Tests\ajax_example\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Functional test of non-AJAX dependent dropdown example.
 *
 * @group ajax_example
 * @group examples
 *
 * @ingroup ajax_example
 *
 * @see \Drupal\Tests\ajax_example\FunctionalJavascript\DynamicFormSectionsTest
 */
class DynamicFormSectionsTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ajax_example'];

  /**
   * Test the dynamic sections form without AJAX.
   */
  public function testDynamicFormSections() {
    // Get the Mink stuff.
    $assert = $this->assertSession();
    $page = $this->getSession()->getPage();

    // Get a URL object for the form, specifying no JS.
    $dropdown_url = Url::fromRoute('ajax_example.dynamic_form_sections', ['nojs' => 'nojs']);

    // Get the form.
    $this->drupalGet($dropdown_url);
    // Check for the initial state.
    $detail_children = $page->findAll('css', 'div.details-wrapper *');
    $this->assertEmpty($detail_children);

    // Go through the dropdown options. First outlier is 'Choose question style'
    // which should have an empty details section.
    $this->drupalGet($dropdown_url);
    $this->submitForm(['question_type_select' => 'Choose question style'], 'Choose');
    $detail_children = $page->findAll('css', 'div.details-wrapper *');
    $this->assertEquals(count($detail_children), 0);

    // Cycle through the other dropdown values.
    $question_styles = [
      'Multiple Choice',
      'True/False',
      'Fill-in-the-blanks',
    ];
    // These all add stuff to the details wrapper.
    foreach ($question_styles as $question_style) {
      $this->drupalGet($dropdown_url);
      $this->submitForm(['question_type_select' => $question_style], 'Choose');
      $detail_children = $page->findAll('css', 'div.details-wrapper *');
      $this->assertNotEquals($this->count($detail_children), 0);
      $this->submitForm(['question' => 'George Washington'], 'Submit your answer');
      $assert->pageTextContains('You got the right answer: George Washington');
    }
    // One wrong answer to exercise that code path.
    $this->drupalGet($dropdown_url);
    $this->submitForm(['question_type_select' => 'Multiple Choice'], 'Choose');
    $detail_children = $page->findAll('css', 'div.details-wrapper *');
    $this->assertNotEquals($this->count($detail_children), 0);
    $this->submitForm(['question' => 'Abraham Lincoln'], 'Submit your answer');
    $assert->pageTextContains('Sorry, your answer (Abraham Lincoln) is wrong');
  }

}
