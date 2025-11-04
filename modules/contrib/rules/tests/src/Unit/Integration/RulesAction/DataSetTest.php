<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Tests\rules\Unit\Integration\RulesIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\DataSet
 * @group RulesAction
 */
class DataSetTest extends RulesIntegrationTestBase {

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
    $this->action = $this->actionManager->createInstance('rules_data_set');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary(): void {
    $this->assertEquals('Set a data value', $this->action->summary());
  }

  /**
   * Tests that primitive values can be set.
   *
   * @covers ::execute
   */
  public function testPrimitiveValues(): void {
    $this->action->setContextValue('data', 'original')
      ->setContextValue('value', 'replacement');
    $this->action->execute();

    $this->assertSame('replacement', $this->action->getContextValue('data'));
    $this->assertSame([], $this->action->autoSaveContext());
  }

  /**
   * Tests that a variable can be set to NULL.
   */
  public function testSetToNull(): void {
    // We don't need to set the 'value' context, it is NULL by default.
    $this->action->setContextValue('data', 'original');
    $this->action->execute();

    $this->assertNull($this->action->getContextValue('data'));
    $this->assertSame([], $this->action->autoSaveContext());
  }

}
