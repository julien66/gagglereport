<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Tests\rules\Unit\Integration\RulesIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\DataListItemAdd
 * @group RulesAction
 */
class DataListItemAddTest extends RulesIntegrationTestBase {

  /**
   * The action to be tested.
   *
   * @var \Drupal\rules\Core\RulesActionInterface
   */
  protected $action;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->action = $this->actionManager->createInstance('rules_list_item_add');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary(): void {
    $this->assertEquals('Add list item', $this->action->summary());
  }

  /**
   * Tests the action execution with default values.
   *
   * @covers ::execute
   */
  public function testActionExecutionWithDefaults(): void {
    // Test adding a value at the end (default position).
    $list = ['One', 'Two', 'Three'];

    $this->action
      ->setContextValue('list', $list)
      ->setContextValue('item', 'Four');

    $this->action->execute();

    // The list should contain four items, with the new item at the end.
    $this->assertEquals([
      'One',
      'Two',
      'Three',
      'Four',
    ], $this->action->getContextValue('list'));
  }

  /**
   * Tests the action execution - item append.
   *
   * @covers ::execute
   */
  public function testActionExecutionItemAppend(): void {
    // Test adding a value at the end.
    $list = ['One', 'Two', 'Three'];

    $this->action
      ->setContextValue('list', $list)
      ->setContextValue('item', 'Four')
      ->setContextValue('position', 'end');

    $this->action->execute();

    // The list should contain four items, with the new item added at the end.
    $this->assertEquals([
      'One',
      'Two',
      'Three',
      'Four',
    ], $this->action->getContextValue('list'));
  }

  /**
   * Tests the action execution - item prepend.
   *
   * @covers ::execute
   */
  public function testActionExecutionItemPrepend(): void {
    // Test adding a value at the start.
    $list = ['One', 'Two', 'Three'];

    $this->action
      ->setContextValue('list', $list)
      ->setContextValue('item', 'Zero')
      ->setContextValue('position', 'start');

    $this->action->execute();

    // The list should contain four items, with the new item added at the start.
    $this->assertEquals([
      'Zero',
      'One',
      'Two',
      'Three',
    ], $this->action->getContextValue('list'));
  }

  /**
   * Tests the action execution - enforce unique items.
   *
   * @covers ::execute
   */
  public function testActionExecutionEnforceUnique(): void {
    // Test unique.
    $list = ['One', 'Two', 'Three', 'Four'];

    $this->action
      ->setContextValue('list', $list)
      ->setContextValue('item', 'Four')
      ->setContextValue('unique', TRUE);

    $this->action->execute();

    // The list should remain the same.
    $this->assertEquals([
      'One',
      'Two',
      'Three',
      'Four',
    ], $this->action->getContextValue('list'));
  }

  /**
   * Tests the action execution - add non-unique items.
   *
   * @covers ::execute
   */
  public function testActionExecutionNonUnique(): void {
    // Test non-unique.
    $list = ['One', 'Two', 'Three', 'Four'];

    $this->action
      ->setContextValue('list', $list)
      ->setContextValue('item', 'Four')
      ->setContextValue('unique', FALSE)
      ->setContextValue('position', 'end');

    $this->action->execute();

    // The list should contain five items, with the new item added at the end.
    $this->assertEquals(['One', 'Two', 'Three', 'Four', 'Four'], $this->action->getContextValue('list'));
  }

}
