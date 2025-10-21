<?php

namespace Drupal\Tests\poll\Kernel\Plugin\migrate\source;

use Drupal\Tests\migrate\Kernel\MigrateSqlSourceTestBase;

/**
 * Tests poll source plugin.
 *
 * @covers \Drupal\poll\Plugin\migrate\source\PollChoice
 *
 * @group poll
 */
class PollChoiceTest extends MigrateSqlSourceTestBase {

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
      'poll_choice' => [
        [
          'chid' => '1',
          'chtext' => 'Poll 1 Choice 1',
          'weight' => '1',
        ],
        [
          'chid' => '2',
          'chtext' => 'Poll 1 Choice 2',
          'weight' => '2',
        ],
        [
          'chid' => '3',
          'chtext' => 'Choice 3',
          'weight' => '3',
        ],
      ],
    ];

    // The expected results are identical to the source data.
    $tests[0]['expected_results'] = $tests[0]['source_data']['poll_choice'];
    $tests[0]['expected_results'][0]['chid'] = '1';
    $tests[0]['expected_results'][0]['chtext'] = 'Poll 1 Choice 1';
    $tests[0]['expected_results'][0]['weight'] = '1';
    $tests[0]['expected_results'][1]['chid'] = '2';
    $tests[0]['expected_results'][1]['chtext'] = 'Poll 1 Choice 2';
    $tests[0]['expected_results'][1]['weight'] = '2';
    $tests[0]['expected_results'][2]['chid'] = '3';
    $tests[0]['expected_results'][2]['chtext'] = 'Choice 3';
    $tests[0]['expected_results'][2]['weight'] = '3';

    return $tests;
  }

}
