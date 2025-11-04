<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Entity;

use Drupal\rules\Entity\ReactionRuleConfig;
use Drupal\Tests\rules\Unit\RulesUnitTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Entity\ReactionRuleConfig
 * @group Rules
 */
class ReactionRuleConfigTest extends RulesUnitTestBase {

  /**
   * Creates a rule.
   *
   * @param array $values
   *   (optional) An array of values to set, keyed by property name.
   */
  protected function createRule(array $values = []): ReactionRuleConfig {
    $values += [
      'id' => 'test_rule',
    ];

    return new ReactionRuleConfig($values, 'rules_reaction_rule');
  }

  /**
   * @covers ::getEvents
   */
  public function testGetEvents(): void {
    // Create a rule with a few events.
    $rule = $this->createRule([
      'events' => [
        ['event_name' => 'foo'],
        ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
      ],
    ]);

    $expected = [
      ['event_name' => 'foo'],
      ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
    ];
    $this->assertSame($expected, $rule->getEvents());
  }

  /**
   * @covers ::getEventNames
   */
  public function testGetEventNames(): void {
    // Create a rule with a few events.
    $rule = $this->createRule([
      'events' => [
        ['event_name' => 'foo'],
        ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
      ],
    ]);

    $expected = ['foo', 'bar'];
    $this->assertSame($expected, $rule->getEventNames());
  }

  /**
   * @covers ::addEvent
   * @covers ::getEvents
   *
   * @dataProvider addEventDataProvider
   */
  public function testAddEvent(array $expected, array $events_init, array $event_add): void {
    $rule = $this->createRule([
      'events' => $events_init,
    ]);
    if (isset($event_add['configuration'])) {
      $this->assertSame($rule, $rule->addEvent($event_add['event_name'], $event_add['configuration']));
    }
    else {
      $this->assertSame($rule, $rule->addEvent($event_add['event_name']));
    }
    $this->assertSame($expected, $rule->getEvents());
  }

  /**
   * Data provider for ::testAddEvent().
   */
  public static function addEventDataProvider(): array {
    return [
      'no events' => [
        'expected' => [['event_name' => 'foo']],
        'events_init' => [],
        'event_add' => ['event_name' => 'foo'],
      ],
      'single event' => [
        'expected' => [
          ['event_name' => 'foo'],
          ['event_name' => 'bar'],
        ],
        'events_init' => [['event_name' => 'foo']],
        'event_add' => ['event_name' => 'bar'],
      ],
      'with config' => [
        'expected' => [
          ['event_name' => 'foo'],
          ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
        ],
        'events_init' => [['event_name' => 'foo']],
        'event_add' => [
          'event_name' => 'bar',
          'configuration' => ['qux' => 'baz'],
        ],
      ],
      'duplicate event' => [
        'expected' => [['event_name' => 'foo']],
        'events_init' => [['event_name' => 'foo']],
        'event_add' => ['event_name' => 'foo'],
      ],
    ];
  }

  /**
   * @covers ::hasEvent
   */
  public function testHasEvent(): void {
    // Create a rule with a few events.
    $rule = $this->createRule([
      'events' => [
        ['event_name' => 'foo'],
        ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
      ],
    ]);

    $this->assertTrue($rule->hasEvent('foo'));
    $this->assertTrue($rule->hasEvent('bar'));
    $this->assertFalse($rule->hasEvent('qux'));
    $this->assertFalse($rule->hasEvent('baz'));
  }

  /**
   * @covers ::removeEvent
   * @covers ::getEvents
   */
  public function testRemoveEvent(): void {
    // Create a rule with a few events.
    $rule = $this->createRule([
      'events' => [
        ['event_name' => 'foo'],
        ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
      ],
    ]);
    $this->assertSame($rule, $rule->removeEvent('bar'));
    $this->assertSame([['event_name' => 'foo']], $rule->getEvents());
  }

  /**
   * @covers ::removeEvent
   * @covers ::getEvents
   */
  public function testRemoveEventWithKeyedIndex(): void {
    // Create a rule with a few events that are numerically indexed.
    // This situation should not ever happen - the configuration entity
    // expects that events are numerically indexed and that the indices
    // are sequential, starting with 0.
    $rule = $this->createRule([
      'events' => [
        2 => ['event_name' => 'foo'],
        3 => ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
      ],
    ]);
    $this->assertSame($rule, $rule->removeEvent('foo'));

    // Removing an event should re-index the events so they are sequential
    // and start with 0.
    $expected = [
      0 => ['event_name' => 'bar', 'configuration' => ['qux' => 'baz']],
    ];
    $this->assertSame($expected, $rule->getEvents());
  }

}
