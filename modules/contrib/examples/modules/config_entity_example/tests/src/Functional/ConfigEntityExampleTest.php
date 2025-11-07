<?php

namespace Drupal\Tests\config_entity_example\Functional;

use Drupal\config_entity_example\Entity\Robot;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the Config Entity Example module.
 *
 * @group config_entity_example
 * @group examples
 *
 * @ingroup config_entity_example
 */
class ConfigEntityExampleTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['config_entity_example'];

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
   * Various functional test of the Config Entity Example module.
   *
   * 1) Verify that the Marvin entity was created when the module was installed.
   *
   * 2) Verify that permissions are applied to the various defined paths.
   *
   * 3) Verify that we can manage entities through the user interface.
   *
   * 4) Verify that the entity we add can be re-edited.
   *
   * 5) Verify that the label is shown in the list.
   */
  public function testConfigEntityExample() {
    $assert = $this->assertSession();

    // 1) Verify that the Marvin entity was created when the module was
    // installed.
    $entity = Robot::load('marvin');
    $this->assertNotNull($entity, 'Marvin was created during installation.');

    // 2) Verify that permissions are applied to the various defined paths.
    // Define some paths. Since the Marvin entity is defined, we can use it
    // in our management paths.
    $forbidden_paths = [
      '/examples/config-entity-example',
      '/examples/config-entity-example/add',
      '/examples/config-entity-example/manage/marvin',
      '/examples/config-entity-example/manage/marvin/delete',
    ];
    // Check each of the paths to make sure we don't have access. At this point
    // we haven't logged in any users, so the client is anonymous.
    foreach ($forbidden_paths as $path) {
      $this->drupalGet($path);
      $assert->statusCodeEquals(403);
    }

    // Create a user with no permissions.
    $without_perms_user = $this->drupalCreateUser();
    $this->drupalLogin($without_perms_user);
    // Should be the same result for forbidden paths, since the user needs
    // special permissions for these paths.
    foreach ($forbidden_paths as $path) {
      $this->drupalGet($path);
      $assert->statusCodeEquals(403);
    }

    // Create a user who can administer robots.
    $admin_user = $this->drupalCreateUser(['administer robots']);
    $this->drupalLogin($admin_user);
    // Forbidden paths are no longer forbidden.
    foreach ($forbidden_paths as $path) {
      $this->drupalGet($path);
      $assert->statusCodeEquals(200);
    }

    // Now that we have the admin user logged in, check the menu links.
    $this->drupalGet('');
    $assert->linkByHrefExists('examples/config-entity-example');

    // 3) Verify that we can manage entities through the user interface.
    // We still have the admin user logged in, so we'll create, update, and
    // delete an entity.
    // Go to the list page.
    $this->drupalGet('/examples/config-entity-example');
    $this->clickLink('Add robot');
    $robot_machine_name = 'wall-e';
    $this->submitForm(['label' => $robot_machine_name, 'id' => $robot_machine_name, 'neural_system' => TRUE], 'Create Robot');

    // 4) Verify that our robot appears when we edit it.
    $this->drupalGet('/examples/config-entity-example/manage/' . $robot_machine_name);
    $assert->fieldExists('label');
    $assert->checkboxChecked('edit-neural-system');

    // 5) Verify that the label and machine name are shown in the list.
    $this->drupalGet('/examples/config-entity-example');
    $this->clickLink('Add robot');
    $robby_machine_name = 'robby_machine_name';
    $robby_label = 'Robby label';
    $this->submitForm(['label' => $robby_label, 'id' => $robby_machine_name, 'neural_system' => TRUE], 'Create Robot');
    $this->drupalGet('/examples/config-entity-example');
    $assert->pageTextContains($robby_label);
    $assert->pageTextContains($robby_machine_name);

    // Try to re-submit the same robot, and verify that we see an error message
    // and not a PHP error.
    $this->drupalGet(Url::fromRoute('entity.robot.add_form'));
    $this->submitForm(['label' => $robby_label, 'id' => $robby_machine_name, 'neural_system' => TRUE], 'Create Robot');
    $assert->pageTextContains('The machine-readable name is already in use.');

    // 6) Verify that required links are present on respective paths.
    $this->drupalGet(Url::fromRoute('entity.robot.list'));
    $this->assertSession()->linkByHrefExists('/examples/config-entity-example/add');
    $this->assertSession()->linkByHrefExists('/examples/config-entity-example/manage/robby_machine_name');
    $this->assertSession()->linkByHrefExists('/examples/config-entity-example/manage/robby_machine_name/delete');

    // Verify links on Add Robot.
    $this->drupalGet('/examples/config-entity-example/add');
    $this->assertActionButton('examples/config-entity-example');

    // Verify links on Edit Robot.
    $this->drupalGet('/examples/config-entity-example/manage/robby_machine_name');
    $this->assertSession()->linkByHrefExists('/examples/config-entity-example/manage/robby_machine_name/delete');
    $this->assertActionButton('examples/config-entity-example');

    // Verify links on Delete Robot.
    $this->drupalGet('/examples/config-entity-example/manage/robby_machine_name/delete');
    // List page will be the destination of the cancel link.
    $cancel_button = $this->xpath(
      '//a[@id="edit-cancel" and contains(@href, :path)]',
      [':path' => '/examples/config-entity-example']
    );
    $this->assertEquals(count($cancel_button), 1, 'Found cancel button linking to list page.');

    // Try to submit a robot with a machine name of 'custom'. This is a reserved
    // keyword we've disallowed in the form.
    $this->drupalGet(Url::fromRoute('entity.robot.add_form'));
    $this->submitForm(['label' => 'Custom', 'id' => 'custom', 'neural_system' => TRUE], 'Create Robot');
    $assert->pageTextContains('Additionally, it can not be the reserved word "custom".');

  }

  /**
   * Wrap an assertion for the action button.
   *
   * @param string $path
   *   Drupal path to a page.
   */
  protected function assertActionButton($path) {
    $button_element = $this->xpath(
      '//a[contains(@class, "button-action") and contains(@data-drupal-link-system-path, :path)]',
      [':path' => $path]
    );
    $this->assertEquals(count($button_element), 1, 'Found action button for path: ' . $path);
  }

}
