<?php

namespace Drupal\Tests\poll\Kernel\migrate\d7;

use Drupal\Core\Database\Database;
use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;
use Drupal\node\Entity\Node;
use Drupal\node\Entity\NodeType;
use Drupal\poll\Entity\Poll;

/**
 * Tests Poll module migrations.
 *
 * @group poll
 */
class MigratePollTest extends MigrateDrupal7TestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'content_translation',
    'comment',
    'datetime',
    'datetime_range',
    'field',
    'file',
    'image',
    'language',
    'link',
    'menu_link_content',
    'menu_ui',
    'node',
    'options',
    'poll',
    'taxonomy',
    'telephone',
    'text',
    'user',
    'views',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->loadFixture(__DIR__ . '/../../../../fixtures/drupal7.php');

    $this->installConfig(static::$modules);

    $this->installEntitySchema('node');
    $this->installEntitySchema('node_type');
    $this->installEntitySchema('poll');
    $this->installEntitySchema('poll_choice');
    $this->installSchema('node', ['node_access']);
    $this->installSchema('poll', ['poll_vote']);

    $this->migrateFields();
    $this->executeMigrations([
      'poll_field_storage_config',
      'poll_field_instance',
      'poll_field_instance_display',
      'poll_field_instance_form_display',
    ]);
    $this->migrateUsers();
    $this->executeMigrations([
      'language',
      'd7_node_complete:poll',
      'poll_choice',
      'poll_question',
    ]);
    $this->executeMigrations([
      'poll_reference',
      'poll_vote',
    ]);
  }

  /**
   * Tests Drupal 7 poll node type migration.
   *
   * Polls are not independent entities in Drupal 7 so when created in Drupal 8
   * the poll entities will be given new id's. Choice ID's should match from
   * source to destination.
   */
  public function testPoll() {
    // Test whether a node type was created.
    $node_type_poll = NodeType::load('poll');
    $this->assertInstanceOf(NodeType::class, $node_type_poll);
    // Check that a reference field to poll is attached to the node type.
    $all_bundle_fields = \Drupal::service('entity_field.manager')->getFieldDefinitions('node', 'poll');
    $this->assertNotEmpty($all_bundle_fields['field_poll'], 'Poll Node type does not have a poll field attached.');

    // Check if Source poll count matches Destination poll count.
    $source_poll_count = $this->sourceDatabase
      ->select('node', 'ns')
      ->fields('ns')
      ->condition('type', 'poll', '=')
      ->countQuery()
      ->execute()
      ->fetchField();

    $destination_poll_count = Database::getConnection()->select('poll', 'p')
      ->fields('p')
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertSame($source_poll_count, $destination_poll_count, 'Number of source & destination polls does not match');

    // Check if Source poll choice count matches Destination poll choice count.
    $source_poll_choice_count = $this->sourceDatabase
      ->select('poll_choice', 'pcs')
      ->fields('pcs')
      ->countQuery()
      ->execute()
      ->fetchField();

    $destination_poll_choice_count = Database::getConnection()->select('poll_choice', 'pc')
      ->fields('pc')
      ->countQuery()
      ->execute()
      ->fetchField();
    $this->assertSame($source_poll_choice_count, $destination_poll_choice_count, 'Number of source & destination poll choices does not match');

    $source_poll_nodes = $this->sourceDatabase
      ->select('node', 'n')
      ->fields('n')
      ->condition('type', 'poll', '=')
      ->execute();

    foreach ($source_poll_nodes as $source_poll_node) {
      $destination_poll_node = Node::load($source_poll_node->nid);

      // Test Node.
      $this->assertInstanceOf(Node::class, $destination_poll_node, 'Poll Node is not a node.');
      $this->assertEquals($source_poll_node->title, $destination_poll_node->title->value, 'Migrated title "' . $destination_poll_node->title->value . '" does not match source title "' . $source_poll_node->title . '"');

      // Test Poll attached to node.
      $polls = $destination_poll_node->field_poll->referencedEntities();

      // There should only be 1 poll attached to a node migrated from Drupal 7.
      $this->assertCount(1, $polls, 'Number of attached polls for node with title "' . $destination_poll_node->title->value . '" is incorrect.');
      $poll = $polls[0];
      $this->assertInstanceOf(Poll::class, $poll);
      $this->assertEquals($source_poll_node->title, $poll->question->value, 'Migrated Poll Question "' . $poll->question->value . '" does not match the source title "' . $source_poll_node->title . '"');

      // Test Poll choices.
      $poll_choices = $poll->choice->referencedEntities();
      $source_poll_choices = $this->sourceDatabase
        ->select('poll_choice', 'pc')
        ->fields('pc', [
          'chid',
          'nid',
          'chtext',
        ])
        ->condition('pc.nid', $source_poll_node->nid, '=')
        ->orderBy('pc.chid', 'ASC')
        ->execute()
        ->fetchAll();

      $this->assertCount(count($source_poll_choices), $poll_choices, 'Number of Migrated choices for Poll with title "' . $poll->question->value . '" do not match amount of choices in source database.');

      $i = 0;
      while ($i < count($source_poll_choices)) {
        $this->assertEquals($source_poll_choices[$i]->chtext, $poll_choices[$i]->choice->value, 'Migrated Poll choice text "' . $poll_choices[$i]->choice->value . '" does not match the source choice text "' . $source_poll_choices[$i]->chtext . '"');
        $this->assertEquals($source_poll_choices[$i]->chid, $poll_choices[$i]->id(), 'Migrated Poll choice id "' . $poll_choices[$i]->id() . '" does not match source chid "' . $source_poll_choices[$i]->chid . '".');
        $i++;
      }
    }

    // Check if Source votes count matches Destination votes count.
    $source_votes_count = $this->sourceDatabase
      ->select('poll_vote', 'pvs')
      ->fields('pvs')
      ->countQuery()
      ->execute()
      ->fetchField();

    $destination_votes_count = Database::getConnection()->select('poll_vote', 'pv')
      ->fields('pv')
      ->countQuery()
      ->execute()
      ->fetchField();

    $this->assertSame($source_votes_count, $destination_votes_count, 'Number of source & destination votes does not match.');

    // Directly compare entries in source database against destination.
    // First get a key/value mapping of source nid with destination pid.
    // Output : $pids[source_nid] = destination_pid.
    $pids = Database::getConnection()->select('migrate_map_poll_question', 'mmpq')
      ->fields('mmpq')
      ->orderBy('sourceid1', 'ASC')
      ->execute()
      ->fetchAllKeyed(1, 3);

    $source_votes = $this->sourceDatabase
      ->select('poll_vote', 'pvs')
      ->fields('pvs')
      ->orderBy('timestamp', 'ASC')
      ->orderBy('chid', 'ASC')
      ->execute();

    foreach ($source_votes as $source_vote) {
      $vote = Database::getConnection()->select('poll_vote', 'pv')
        ->fields('pv')
        ->condition('pv.timestamp', $source_vote->timestamp)
        ->condition('pv.chid', $source_vote->chid)
        ->execute()
        ->fetchObject();

      $this->assertSame($source_vote->chid, $vote->chid, 'Migrated vote chid does not match');
      $this->assertSame($source_vote->hostname, $vote->hostname, 'Migrated vote hostname does not match');
      $this->assertSame($source_vote->timestamp, $vote->timestamp, 'Migrated vote timestamp does not match');
      $this->assertSame($source_vote->uid, $vote->uid, 'Migrated vote uid does not match');
      $this->assertSame($pids[$source_vote->nid], $vote->pid, 'Migrated vote pid does not match');
    }

  }

}
