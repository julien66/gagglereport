<?php

namespace Drupal\Tests\ajax_example\FunctionalJavascript;

use Drupal\Core\Url;
use Drupal\FunctionalJavascriptTests\WebDriverTestBase;

/**
 * Test the user interactions for the Ajax Wizard example.
 *
 * @group ajax_example
 * @group examples
 */
class AjaxWizardTest extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['ajax_example'];

  /**
   * Test that we can successfully submit the wizard following the steps.
   *
   * It verifies that each step displays the correct fields and we can finally
   * see the success message once we submit the form.
   */
  public function testWizardSteps() {
    // Get our Mink stuff.
    $session = $this->getSession();
    $page = $session->getPage();
    $assert = $this->assertSession();

    // Get the page.
    $form_url = Url::fromRoute('ajax_example.wizard');
    $this->drupalGet($form_url);

    // Check our initial state.
    $assert->fieldExists('name');
    $assert->fieldNotExists('address');
    $assert->fieldNotExists('city');
    $assert->buttonExists('Next step');
    $assert->buttonNotExists('Previous step');
    $assert->buttonNotExists('Submit your information');

    // We fill the  name at first step and continue with the second step.
    $page->fillField('name', 'Sherlock Holmes');
    $page->pressButton('Next step');
    $assert->assertWaitOnAjaxRequest();

    // Check the state of the second step.
    $assert->fieldExists('address');
    $assert->fieldNotExists('name');
    $assert->fieldNotExists('city');
    $assert->buttonExists('Next step');
    $assert->buttonExists('Previous step');
    $assert->buttonNotExists('Submit your information');

    // We fill the address at the second step and continue with the last step.
    $page->fillField('address', '221B Baker Street');
    $page->pressButton('Next step');
    $assert->assertWaitOnAjaxRequest();

    // Check the state of the third step.
    $assert->fieldExists('city');
    $assert->fieldNotExists('name');
    $assert->fieldNotExists('address');
    $assert->buttonNotExists('Next step');
    $assert->buttonExists('Previous step');
    $assert->buttonExists('Submit your information');

    // We fill the city at the third step, and we finally submit the form.
    $page->fillField('city', 'London');
    $page->pressButton('Submit your information');

    // We check the output and assert that the already set values are displayed.
    $assert->pageTextContains('Your information has been submitted:');
    $assert->pageTextContains('Name: Sherlock Holmes');
    $assert->pageTextContains('Address: 221B Baker Street');
    $assert->pageTextContains('City: London');
  }

  /**
   * Test that the previous values are correctly displayed.
   *
   * If we move back to previous steps the already set values should be
   * displayed.
   */
  public function testWizardPreviousStepsValues() {
    // Get our Mink stuff.
    $session = $this->getSession();
    $page = $session->getPage();
    $assert = $this->assertSession();

    // Get the page.
    $form_url = Url::fromRoute('ajax_example.wizard');
    $this->drupalGet($form_url);

    // We fill the first step and continue.
    $page->fillField('name', 'Sherlock Holmes');
    $page->pressButton('Next step');
    $assert->assertWaitOnAjaxRequest();

    // We fill the second step and continue with the last step.
    $page->fillField('address', '221B Baker Street');
    $page->pressButton('Next step');
    $assert->assertWaitOnAjaxRequest();

    // We fill the third step, and we finally submit the form.
    $page->fillField('city', 'London');

    // Now we move back to previous steps and check that the values are still
    // there.
    $page->pressButton('Previous step');
    $assert->assertWaitOnAjaxRequest();

    $assert->fieldValueEquals('address', '221B Baker Street');
    $page->pressButton('Previous step');
    $assert->assertWaitOnAjaxRequest();

    $assert->fieldValueEquals('name', 'Sherlock Holmes');
  }

}
