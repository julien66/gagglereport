<?php

namespace Drupal\Tests\poll\Kernel\Plugin\migrate\source;

use Drupal\Tests\migrate\Kernel\MigrateSqlSourceTestBase;

/**
 * Tests poll source plugin.
 *
 * @covers \Drupal\poll\Plugin\migrate\source\PollVote
 *
 * @group poll
 */
class PollVoteTest extends MigrateSqlSourceTestBase {

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
      'poll_vote' => [
        [
          'chid' => '2',
          'nid' => '101',
          'uid' => '0',
          'hostname' => '127.0.0.1',
          'timestamp' => '1571878591',
        ],
        [
          'chid' => '2',
          'nid' => '101',
          'uid' => '1',
          'hostname' => '127.0.0.1',
          'timestamp' => '1571878387',
        ],
        [
          'chid' => '5',
          'nid' => '102',
          'uid' => '0',
          'hostname' => '127.0.0.1',
          'timestamp' => '1571878594',
        ],
      ],
    ];

    // The expected results are identical to the source data.
    $tests[0]['expected_results'] = $tests[0]['source_data']['poll_vote'];
    $tests[0]['expected_results'][0]['chid'] = '2';
    $tests[0]['expected_results'][0]['nid'] = '101';
    $tests[0]['expected_results'][0]['uid'] = '0';
    $tests[0]['expected_results'][0]['hostname'] = '127.0.0.1';
    $tests[0]['expected_results'][0]['timestamp'] = '1571878591';
    $tests[0]['expected_results'][1]['chid'] = '2';
    $tests[0]['expected_results'][1]['nid'] = '101';
    $tests[0]['expected_results'][1]['uid'] = '1';
    $tests[0]['expected_results'][1]['hostname'] = '127.0.0.1';
    $tests[0]['expected_results'][1]['timestamp'] = '1571878387';
    $tests[0]['expected_results'][2]['chid'] = '5';
    $tests[0]['expected_results'][2]['nid'] = '102';
    $tests[0]['expected_results'][2]['uid'] = '0';
    $tests[0]['expected_results'][2]['hostname'] = '127.0.0.1';
    $tests[0]['expected_results'][2]['timestamp'] = '1571878594';

    return $tests;
  }

}
