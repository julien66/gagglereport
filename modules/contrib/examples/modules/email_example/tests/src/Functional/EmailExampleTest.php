<?php

namespace Drupal\Tests\email_example\Functional;

use Drupal\Core\Test\AssertMailTrait;
use Drupal\Tests\examples\Functional\ExamplesBrowserTestBase;

/**
 * Tests for the email_example module.
 *
 * @ingroup email_example
 *
 * @group email_example
 * @group examples
 */
class EmailExampleTest extends ExamplesBrowserTestBase {

  use AssertMailTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['email_example'];

  /**
   * The installation profile to use with this test.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * Test our new email form.
   *
   * Tests for the following:
   *
   * - A link to the email_example in the Tools menu.
   * - That you can successfully access the email_example page.
   */
  public function testEmailExampleBasic() {
    $assert = $this->assertSession();
    // Test for a link to the email_example in the Tools menu.
    $this->drupalGet('');
    $assert->statusCodeEquals(200);
    $assert->linkByHrefExists('examples/email-example');

    // Verify if we can successfully access the email_example page.
    $this->drupalGet('examples/email-example');
    $assert->statusCodeEquals(200);

    // Verify email form has the email and message fields.
    $assert->fieldExists('edit-email');
    $assert->fieldExists('edit-message');

    // Verify the email form is submitted.
    $edit = ['email' => 'example@example.com', 'message' => 'test'];
    $this->drupalGet('examples/email-example');
    $this->submitForm($edit, 'Submit');
    $assert->statusCodeEquals(200);

    // Verify the confirmation message is shown.
    $assert->pageTextContains('Your message has been sent.');

    // Verify the email contains the correct values for its fields.
    $from = $this->config('system.site')->get('mail');
    $this->assertMailString('to', $edit['email'], 1);
    $this->assertMailString('subject', "Email sent from $from", 1);
    $this->assertMailString('body', $edit['message'], 1);
    $this->assertMailString('body', "\n--\nMail altered by email_example module.", 1);
  }

}
