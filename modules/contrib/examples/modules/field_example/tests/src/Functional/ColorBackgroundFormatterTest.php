<?php

namespace Drupal\Tests\field_example\Functional;

use Drupal\Component\Render\FormattableMarkup;

/**
 * Test the basic functionality of Color background formatter.
 *
 * Create a content type with a field_example_rgb field, configure it with the
 * color_background_formatter, create a node and check for correct values.
 *
 * @group field_example
 * @group examples
 *
 * @ingroup field_example
 */
class ColorBackgroundFormatterTest extends FieldExampleBrowserTestBase {

  /**
   * Field example scenario tests.
   *
   * The following scenarios:
   * - Creates a content type.
   * - Adds a multivalued field_example_rgb to it.
   * - Creates a node of the new type.
   * - Populates the multivalued field with two items.
   * - Tests the result.
   */
  public function testSingleValueField() {
    $assert = $this->assertSession();
    // Login with Admin and create a field.
    $this->drupalLogin($this->administratorAccount);
    $this->fieldName = $this->createField('field_example_rgb', 'field_example_color_picker', '1', 'field_example_color_background');

    // Login with Author user for content creation.
    $this->drupalLogin($this->authorAccount);
    $this->drupalGet('node/add/' . $this->contentTypeName);

    // Details to be submitted for content creation.
    $title = $this->randomMachineName(20);
    $color = '#00ff00';
    $edit = [
      'title[0][value]' => $title,
      'field_' . $this->fieldName . '[0][value]' => $color,
    ];

    // Submit the content creation form.
    $this->submitForm($edit, 'Save');
    $assert->pageTextContains((string) new FormattableMarkup('@type @title has been created', ['@type' => $this->contentTypeName, '@title' => $title]));

    // Verify color.
    $assert->pageTextContains('The content area color has been changed to ' . $color);

    // Test the formatter's configuration options. First verify the foreground
    // color calculation logic is enabled by default and working.
    $assert->elementAttributeNotContains('css', 'p[style*="background-color: ' . $color . '"]', 'style', 'color: inherit');
    $assert->elementAttributeContains('css', 'p[style*="background-color: ' . $color . '"]', 'style', 'color: black');

    // Then toggle the setting off.
    \Drupal::service('entity_display.repository')
      ->getViewDisplay('node', $this->contentTypeName)
      ->setComponent('field_' . $this->fieldName, [
        'label' => 'inline',
        'weight' => 20,
        'type' => 'field_example_color_background',
        'settings' => [
          'adjust_text_color' => 0,
        ],
      ])
      ->save();

    // Clear the cache to ensure we get updated field output.
    // drupal_flush_all_caches();
    $this->getSession()->reload();
    $assert = $this->assertSession();

    $assert->elementAttributeContains('css', 'p[style*="background-color: ' . $color . '"]', 'style', 'color: inherit');
    $assert->elementAttributeNotContains('css', 'p[style*="background-color: ' . $color . '"]', 'style', 'color: black');
  }

  /**
   * Tests a multi-value field.
   *
   * Test the following scenarios in Field example:
   * - Creates a content type.
   * - Adds a multivalued field_example_rgb to it.
   * - Creates a node of the new type.
   * - Populates the multivalued field with two items.
   * - Tests the result.
   */
  public function testMultiValueField() {
    $assert = $this->assertSession();

    // Login with Admin and create a field.
    $this->drupalLogin($this->administratorAccount);
    $this->fieldName = $this->createField('field_example_rgb', 'field_example_color_picker', '-1', 'field_example_color_background');

    // Login with Author user for content creation.
    $this->drupalLogin($this->authorAccount);
    $this->drupalGet('node/add/' . $this->contentTypeName);

    // Details to be submitted for content creation.
    $title = $this->randomMachineName(20);
    $edit = [
      'title[0][value]' => $title,
      'field_' . $this->fieldName . '[0][value]' => '#00ff00',
    ];

    // Add another field value.
    $this->submitForm($edit, 'Add another item');

    // Set value for newly added item.
    $edit = [
      'field_' . $this->fieldName . '[1][value]' => '#ffff4f',
    ];

    // Submit the content creation form.
    $this->submitForm($edit, 'Save');
    $assert->pageTextContains((string) new FormattableMarkup('@type @title has been created', ['@type' => $this->contentTypeName, '@title' => $title]));

    // Verify color.
    $assert->pageTextContains('The content area color has been changed to #00ff00');
    $assert->pageTextContains('The content area color has been changed to #ffff4f');
  }

}
