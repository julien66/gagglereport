<?php

namespace Drupal\poll;

use Drupal\views\EntityViewsData;

/**
 * Render controller for polls.
 */
class PollViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['poll_field_data']['votes'] = [
      'title' => 'Total votes',
      'help' => 'Displays the total number of votes.',
      'real field' => 'id',
      'field' => [
        'id' => 'poll_totalvotes',
      ],
    ];

    $data['poll_field_data']['status_with_runtime'] = [
      'title' => 'Active with runtime',
      'help' => 'Displays the status with runtime.',
      'real field' => 'id',
      'field' => [
        'id' => 'poll_status',
      ],
    ];

    return $data;
  }

}
