<?php

namespace Drupal\field_permission_example\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;

/**
 * Plugin implementation of our "sticky-note" formatter.
 *
 * @FieldFormatter(
 *   id = "field_permission_example_simple_formatter",
 *   module = "field_permission_example",
 *   label = @Translation("Simple text-based formatter"),
 *   field_types = {
 *     "field_permission_example_field_note"
 *   }
 * )
 */
class SimpleTextFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        // We wrap the field note content up in a div tag.
        '#type' => 'html_tag',
        '#tag' => 'div',
        // This text is auto-XSS escaped. See docs for the html_tag element.
        '#value' => $item->value,
        // Let's give the note a nice sticky-note CSS appearance.
        '#attributes' => [
          'class' => 'sticky-note',
        ],
        // This is the CSS for the sticky note.
        '#attached' => [
          'library' => ['field_permission_example/field_note.sticky'],
        ],
      ];
    }

    return $elements;
  }

}
