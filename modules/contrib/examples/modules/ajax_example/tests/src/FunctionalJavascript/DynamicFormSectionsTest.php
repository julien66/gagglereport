<?php

namespace Drupal\Tests\ajax_example\FunctionalJavascript;

use Drupal\Core\Url;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Functional test of dependent dropdown example.
 *
 * @group ajax_example
 *
 * @see \Drupal\Tests\ajax_example\Functional\DynamicFormSectionsTest
 */
class DynamicFormSectionsTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ajax_example'];

  /**
   * Test the dependent dropdown form with AJAX.
   */
  public function testDynamicFormSections() {
    // Get the Mink stuff.
    $assert = $this->assertSession();
    $page = $this->getSession()->getPage();

    // Get a URL object for the form, specifying no JS.
    $dropdown_url = Url::fromRoute('ajax_example.dynamic_form_sections', ['nojs' => 'ajax']);

    // Get the form.
    $this->drupalGet($dropdown_url);
    // Check for the initial state.
    $this->assertEmpty($page->findAll('css', 'questions-wrapper *'));

    // Cycle through the other dropdown values.
    $question_styles = [
      'Multiple Choice',
      'True/False',
      'Fill-in-the-blanks',
    ];

    // Check expectations against the details wrapper.
    foreach ($question_styles as $question_style) {
      $assert->selectExists('question_type_select')->selectOption($question_style);
      $assert->assertWaitOnAjaxRequest();
      $this->assertNotEmpty($page->findAll('css', '.questions-wrapper *'));
    }
    // Prompt to choose question should remove the question.
    $assert->selectExists('question_type_select')->selectOption('Choose question style');
    $assert->assertWaitOnAjaxRequest();
    $this->assertEmpty($page->findAll('css', 'questions-wrapper *'));

    // Submit the correct answers.
    foreach ($question_styles as $question_style) {
      $this->drupalGet($dropdown_url);
      $assert->selectExists('question_type_select')->selectOption($question_style);
      $assert->assertWaitOnAjaxRequest();
      $this->submitForm(['question' => 'George Washington'], 'Submit your answer');
      $assert->pageTextContains('You got the right answer: George Washington');
    }
  }

}
