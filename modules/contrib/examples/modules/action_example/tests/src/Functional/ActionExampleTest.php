<?php

namespace Drupal\Tests\action_example\Functional;

use Drupal\Tests\examples\Functional\ExamplesBrowserTestBase;

/**
 * Default test case for the action_example module.
 *
 * @group action_example
 * @group examples
 */
class ActionExampleTest extends ExamplesBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['action_example'];

  /**
   * The installation profile to use with this test.
   *
   * We need the 'minimal' profile in order to make sure the Tool block is
   * available.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * Test Action Example.
   *
   * 1. action_example_basic_action: Configure a action_example_basic_action to
   *    happen when user logs in.
   * 2. action_example_unblock_user_action: When a user's profile is being
   *    viewed, unblock that user.
   * 3. action_example_node_sticky_action: Create a user, configure that user
   *    to always be stickied using advanced configuration. Have the user
   *    create content; verify that it gets stickied.
   */
  public function testActionExample() {
    // Create an administrative user.
    $admin_user = $this->drupalCreateUser(['administer actions']);
    $this->drupalLogin($admin_user);

    $this->drupalGet('/admin/config/system/actions');
    $this->assertSession()->pageTextContains('An action that does nothing');

  }

}
