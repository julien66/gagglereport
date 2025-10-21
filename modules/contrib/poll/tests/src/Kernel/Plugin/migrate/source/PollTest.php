<?php

namespace Drupal\Tests\poll\Kernel\Plugin\migrate\source;

use Drupal\Tests\migrate\Kernel\MigrateSqlSourceTestBase;

/**
 * Tests poll source plugin.
 *
 * @covers \Drupal\poll\Plugin\migrate\source\Poll
 *
 * @group poll
 */
class PollTest extends MigrateSqlSourceTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['poll', 'migrate_drupal'];

  /**
   * {@inheritdoc}
   */
  public function providerSource() {
    $tests = [];

    // The source data.
    $tests[0]['source_data'] = [
      'node' => [
        [
          'nid' => 1,
          'vid' => 1,
          'type' => 'poll',
          'language' => 'en',
          'title' => 'Poll title 1',
          'uid' => 1,
          'status' => 1,
          'created' => 1279051598,
          'changed' => 1279051598,
          'comment' => 2,
          'promote' => 1,
          'sticky' => 0,
          'tnid' => 0,
          'translate' => 0,
        ],
        [
          'nid' => 2,
          'vid' => 2,
          'type' => 'poll',
          'language' => 'en',
          'title' => 'Poll title 2',
          'uid' => 1,
          'status' => 1,
          'created' => 1279290908,
          'changed' => 1279308993,
          'comment' => 0,
          'promote' => 1,
          'sticky' => 0,
          'tnid' => 0,
          'translate' => 0,
        ],
        [
          'nid' => 4,
          'vid' => 2,
          'type' => 'page',
          'language' => 'en',
          'title' => 'Page title 2',
          'uid' => 1,
          'status' => 1,
          'created' => 1279290908,
          'changed' => 1279308993,
          'comment' => 0,
          'promote' => 1,
          'sticky' => 0,
          'tnid' => 0,
          'translate' => 0,
        ],
      ],
      'poll' => [
        [
          'nid' => '1',
          'runtime' => '0',
          'active' => '1',
        ],
        [
          'nid' => '2',
          'runtime' => '31536000',
          'active' => '1',
        ],
        [
          'nid' => '3',
          'runtime' => '0',
          'active' => '1',
        ],
      ],
      'poll_choice' => [
        [
          'chid' => 'foo',
          'nid' => '`',
          'chtext' => 'barr',
          'weight' => '9',
        ],
      ],
    ];

    // The expected results are identical to the first two polls in the
    // source data as we don't expect a page to be referenced, not a poll to be
    // created with an unknown nid as reference.
    $tests[0]['expected_results'][] = $tests[0]['source_data']['poll'][0];
    $tests[0]['expected_results'][] = $tests[0]['source_data']['poll'][1];
    $tests[0]['expected_results'][0]['runtime'] = '0';
    $tests[0]['expected_results'][0]['active'] = '1';
    $tests[0]['expected_results'][1]['runtime'] = '31536000';
    $tests[0]['expected_results'][1]['active'] = '1';

    return $tests;
  }

}
