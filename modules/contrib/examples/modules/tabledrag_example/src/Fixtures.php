<?php

namespace Drupal\tabledrag_example;

/**
 * Provides sample data for module's examples.
 */
class Fixtures {

  /**
   * Returns array of sample records for demo purposes.
   *
   * @return array
   *   Array of sample records.
   *
   * @see \Drupal\tabledrag_example\Form\TableDragExampleResetForm::submitForm()
   * @see tabledrag_example_install()
   */
  public static function getSampleItems() {
    return [
      [
        'name' => 'Item One',
        'description' => 'The first item',
        'item_group' => 'Group1',
      ],
      [
        'name' => 'Item Two',
        'description' => 'The second item',
        'item_group' => 'Group1',
      ],
      [
        'name' => 'Item Three',
        'description' => 'The third item',
        'item_group' => 'Group1',
      ],
      [
        'name' => 'Item Four',
        'description' => 'The fourth item',
        'item_group' => 'Group2',
      ],
      [
        'name' => 'Item Five',
        'description' => 'The fifth item',
        'item_group' => 'Group2',
      ],
      [
        'name' => 'Item Six',
        'description' => 'The sixth item',
        'item_group' => 'Group2',
      ],
      [
        'name' => 'Item Seven',
        'description' => 'The seventh item',
        'item_group' => 'Group3',
      ],
      [
        'name' => 'Item Eight',
        'description' => 'The eighth item',
        'item_group' => 'Group3',
      ],
      [
        'name' => 'Item Nine',
        'description' => 'The ninth item',
        'item_group' => 'Group3',
      ],
      [
        'name' => 'Item Ten',
        'description' => 'The tenth item',
        'item_group' => 'Group4',
      ],
      [
        'name' => 'Item Eleven — A Root Node',
        'description' => 'This item cannot be nested under a parent item',
        'item_group' => 'Group4',
      ],
      [
        'name' => 'Item Twelve — A Leaf Item',
        'description' => 'This item cannot have child items',
        'item_group' => 'Group4',
      ],
    ];
  }

}
