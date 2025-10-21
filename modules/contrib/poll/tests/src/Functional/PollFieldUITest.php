<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Tests\field_ui\Traits\FieldUiTestTrait;

/**
 * Tests the poll field UI.
 *
 * @group poll
 */
class PollFieldUITest extends PollTestBase {

  use FieldUiTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field_ui',
    'link',
    'help',
  ];

  /**
   * {@inheritdoc}
   */
  protected $adminPermissions = [
    'administer poll form display',
    'administer poll display',
    'administer poll fields',
    'administer polls',
    'access polls',
    'access administration pages',
    'administer blocks',
    'administer permissions',
  ];

  /**
   * Test if 'Manage fields' page is visible in the poll's settings UI.
   */
  public function testPollFieldUi() {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/content/poll');
    $this->assertSession()->statusCodeEquals(200);

    // Check if 'Manage fields' tab appears in the poll's settings page.
    $this->assertSession()->addressEquals('admin/config/content/poll');
    $this->assertSession()->pageTextContains('Manage fields');

    // Ensure that the 'Manage display' page is visible.
    $this->clickLink('Manage display');
    $this->assertSession()->titleEquals('Manage display | Drupal');

    // Ensure vote results in List.
    $element = $this->cssSelect('#poll-votes');
    $this->assertNotEquals($element, [], '"Vote form/Results" field is available.');

    // Ensure that the 'Manage fields' page is visible.
    $this->clickLink('Manage fields');
    $this->assertSession()->titleEquals('Manage fields | Drupal');

    // Add a new field with Field UI.
    $storage_edit = ['settings[target_type]' => 'poll'];
    $this->fieldUIAddNewField('admin/config/content/poll', 'poll', 'poll', 'field_ui:entity_reference:node', $storage_edit);
  }

  /**
   * Tests if the Poll Help-page is working properly.
   */
  public function testPollHelpPage() {
    // Help pages have an new permission in Drupal 10.2.
    // @see: https://www.drupal.org/node/3344060.
    if (version_compare(\Drupal::VERSION, '10.2', '>=')) {
      $this->createRole(['access help pages'], 'helppages', 'helppages');
      $this->adminUser->addRole('helppages')->save();
    }

    $this->drupalLogin($this->adminUser);

    // Check access to the help overview.
    $this->drupalGet('admin/help');
    $this->assertSession()->pageTextContains('Help');
    $this->assertSession()->pageTextContains('Module overviews');
    $this->assertSession()->pageTextContains('Poll');

    // Go to poll page.
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Add poll');
    $this->assertSession()->addressEquals('poll/add');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Polls', 0);
    $this->assertSession()->addressEquals('admin/content/poll');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Polls', 1);
    $this->assertSession()->addressEquals('admin/content/poll');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Blocks administration page');
    $this->assertSession()->addressEquals('admin/structure/block');
    $this->drupalGet('admin/help/poll');

    $this->clickLink('Configure Poll permissions');
    $this->assertSession()->pageTextContains('Access the Poll overview page');
  }

}
