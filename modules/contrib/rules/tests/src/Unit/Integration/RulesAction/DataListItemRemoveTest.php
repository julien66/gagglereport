<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Tests\rules\Unit\Integration\RulesIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\DataListItemRemove
 * @group RulesAction
 */
class DataListItemRemoveTest extends RulesIntegrationTestBase {

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

    $this->action = $this->actionManager->createInstance('rules_list_item_remove');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary(): void {
    $this->assertEquals('Remove item from list', $this->action->summary());
  }

  /**
   * Tests the action execution.
   *
   * @covers ::execute
   */
  public function testActionExecution(): void {
    $list = ['One', 'Two', 'Three'];

    $this->action
      ->setContextValue('list', $list)
      ->setContextValue('item', 'Two');

    $this->action->execute();

    // The second item should be removed from the list.
    $this->assertEquals(['One', 'Three'], array_values($this->action->getContextValue('list')));
  }

}
