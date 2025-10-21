<?php

namespace Drupal\Tests\poll\Functional;

use Drupal\Tests\field_ui\Traits\FieldUiTestTrait;

/**
 * Tests the poll fields.
 *
 * @group poll
 */
class PollFieldTest extends PollTestBase {
  use FieldUiTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'field_ui',
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
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Add breadcrumb block.
    $this->drupalPlaceBlock('system_breadcrumb_block');
  }

  /**
   * Test poll entity fields.
   */
  public function testPollFields() {
    $poll = $this->poll;
    $this->drupalLogin($this->adminUser);
    // Add some fields.
    $this->fieldUIAddNewField('admin/config/content/poll', 'number', 'Number field', 'integer');
    $this->fieldUIAddNewField('admin/config/content/poll', 'text', 'Text field', 'string');

    // Test field form display.
    $this->drupalGet('admin/config/content/poll/form-display');
    $this->assertSession()->pageTextContains('Number field');
    $this->assertSession()->pageTextContains('Text field');
    // Change field weights.
    $edit = [
      'fields[field_number][weight]' => '5',
      'fields[field_text][weight]' => '4',
    ];
    $this->submitForm($edit, 'Save');
    $this->assertSession()->fieldValueEquals('fields[field_number][weight]', '5');
    $this->assertSession()->fieldValueEquals('fields[field_text][weight]', '4');

    // Test field display.
    $this->drupalGet('admin/config/content/poll/display');
    $this->assertSession()->pageTextContains('Number field');
    $this->assertSession()->pageTextContains('Text field');
    // Change field weights.
    $edit = [
      'fields[field_number][weight]' => '2',
      'fields[field_text][weight]' => '1',
    ];
    $this->submitForm($edit, 'Save');
    $this->assertSession()->fieldValueEquals('fields[field_number][weight]', '2');
    $this->assertSession()->fieldValueEquals('fields[field_text][weight]', '1');

    // Test edit poll form.
    $this->drupalGet('poll/' . $poll->id() . '/edit');
    $this->assertSession()->pageTextContains('Number field');
    $this->assertSession()->pageTextContains('Text field');
    // Check fields positions.
    $field_text_div = $this->getSession()->getPage()->find('xpath', '//form[@id="poll-edit-form"]/div[13]');
    $this->assertEquals('edit-field-text-wrapper', $field_text_div->getAttribute('id'));
    $field_number_div = $this->getSession()->getPage()->find('xpath', '//form[@id="poll-edit-form"]/div[14]');
    $this->assertEquals('edit-field-number-wrapper', $field_number_div->getAttribute('id'));
    $edit = [
      'field_number[0][value]' => random_int(10, 1000),
      'field_text[0][value]' => $this->randomString(),
    ];
    $this->submitForm($edit, 'Save');

    // Test view poll form.
    $this->drupalGet('poll/' . $poll->id());
    $this->assertSession()->pageTextContains('Number field');
    $this->assertSession()->pageTextContains('Text field');
    // Check fields positions.
    $field_text_div = $this->getSession()->getPage()->find('xpath', '//div[@class="poll viewmode-full"]/div[1]');
    $this->assertEquals('field field--name-field-text field--type-string field--label-above', $field_text_div->getAttribute('class'));
    $field_number_div = $this->getSession()->getPage()->find('xpath', '//div[@class="poll viewmode-full"]/div[2]');
    $this->assertEquals('field field--name-field-number field--type-integer field--label-above', $field_number_div->getAttribute('class'));
  }

}
