<?php

namespace Drupal\Tests\config_entity_example\Functional;

use Drupal\config_entity_example\Entity\Robot;
use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Ensure Robot entities can be used in entity_reference fields.
 *
 * @group config_entity_example
 * @group examples
 *
 * @ingroup config_entity_example
 */
class RobotReferenceTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['config_entity_example', 'node', 'field_ui'];

  /**
   * {@inheritdoc}
   *
   * We use the minimal profile because otherwise local actions aren't placed in
   * a block anywhere.
   */
  protected $profile = 'minimal';

  /**
   * Ensure we can use robot entities as reference fields.
   */
  public function testEntityReference() {
    $assert = $this->assertSession();

    $type = $this->createContentType();

    $this->drupalLogin($this->createUser([
      'create ' . $type->id() . ' content',
      'administer node fields',
    ]));

    // - Go to the "manage fields" section of a content entity.
    $this->drupalGet('admin/structure/types/manage/' . $type->id() . '/fields');
    $assert->statusCodeEquals(200);

    // - Click on the "add field" button.
    $this->clickLink('Create a new field');

    // - Under "Reference" select "other".
    // - Choose a label and click continue.
    $this->submitForm(['new_storage_type' => 'entity_reference', 'field_name' => 'robot_reference', 'label' => 'robot_reference'], 'Save and continue');
    $assert->statusCodeEquals(200);

    // - Under configuration select "robot".
    $this->submitForm(['settings[target_type]' => 'robot'], 'Save field settings');
    $assert->statusCodeEquals(200);

    // - Create a content entity containing the created reference field. Select
    //   "Marvin, the paranoid android".
    // - Click save.
    $robot = Robot::loadMultiple();
    /** @var \Drupal\config_entity_example\Entity\Robot $robot */
    $robot = reset($robot);
    $this->drupalGet(Url::fromRoute('node.add', ['node_type' => $type->id()]));
    $this->submitForm(['title[0][value]' => 'title', 'field_robot_reference[0][target_id]' => $robot->label()], 'Save');
    $assert->statusCodeEquals(200);
    $assert->pageTextContains($robot->label());
  }

}
