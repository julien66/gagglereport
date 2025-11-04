<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\rules\Context\ExecutionStateInterface;
use Drupal\rules\Engine\ConditionExpressionContainer;
use Drupal\rules\Engine\ConditionExpressionContainerInterface;
use Drupal\rules\Engine\ExpressionManagerInterface;

/**
 * @coversDefaultClass \Drupal\rules\Engine\ConditionExpressionContainer
 * @group Rules
 */
class RulesConditionContainerTest extends RulesUnitTestBase {

  /**
   * Creates a mocked condition container.
   *
   * @param array $methods
   *   The methods to mock.
   * @param string $class
   *   The name of the created mock class.
   *
   * @return \Drupal\rules\Engine\ConditionExpressionContainerInterface
   *   The mocked condition container.
   */
  protected function getMockConditionContainer(array $methods = [], string $class = 'RulesConditionContainerMock'): ConditionExpressionContainerInterface {
    return $this->getMockForAbstractClass(
      ConditionExpressionContainer::class, [
        [],
        'test_id',
        [],
        $this->prophesize(ExpressionManagerInterface::class)->reveal(),
        $this->prophesize(LoggerChannelInterface::class)->reveal(),
      ], $class, TRUE, TRUE, TRUE, $methods
    );
  }

  /**
   * Tests adding conditions to the condition container.
   *
   * @covers ::addExpressionObject
   */
  public function testAddExpressionObject(): void {
    $container = $this->getMockConditionContainer();
    $container->addExpressionObject($this->trueConditionExpression->reveal());

    $property = new \ReflectionProperty($container, 'conditions');
    $property->setAccessible(TRUE);

    $this->assertEquals([$this->trueConditionExpression->reveal()], array_values($property->getValue($container)));
  }

  /**
   * Tests negating the result of the condition container.
   *
   * @covers ::negate
   * @covers ::isNegated
   */
  public function testNegate(): void {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [], '', FALSE);

    $this->assertFalse($container->isNegated());
    $this->assertTrue($container->execute());

    $container->negate(TRUE);
    $this->assertTrue($container->isNegated());
    $this->assertFalse($container->execute());
  }

  /**
   * Tests executing the condition container.
   *
   * @covers ::execute
   */
  public function testExecute(): void {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [], '', FALSE);
    $this->assertTrue($container->execute());
  }

  /**
   * Tests that an expression can be retrieved by UUID.
   */
  public function testLookupExpression(): void {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      $this->prophesize(LoggerChannelInterface::class)->reveal(),
    ], '', TRUE);
    $container->addExpressionObject($this->trueConditionExpression->reveal());
    $uuid = $this->trueConditionExpression->reveal()->getUuid();
    $this->assertSame($this->trueConditionExpression->reveal(), $container->getExpression($uuid));
    $this->assertFalse($container->getExpression('invalid UUID'));
  }

  /**
   * Tests that a nested expression can be retrieved by UUID.
   */
  public function testLookupNestedExpression(): void {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      $this->prophesize(LoggerChannelInterface::class)->reveal(),
    ], '', TRUE);
    $container->addExpressionObject($this->trueConditionExpression->reveal());

    $nested_container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      $this->prophesize(LoggerChannelInterface::class)->reveal(),
    ], '', TRUE);
    $nested_container->addExpressionObject($this->falseConditionExpression->reveal());

    $container->addExpressionObject($nested_container);

    $uuid = $this->falseConditionExpression->reveal()->getUuid();
    $this->assertSame($this->falseConditionExpression->reveal(), $container->getExpression($uuid));
  }

  /**
   * Tests deleting a condition from the container.
   */
  public function testDeletingCondition(): void {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      $this->prophesize(LoggerChannelInterface::class)->reveal(),
    ], '', TRUE);
    $container->addExpressionObject($this->trueConditionExpression->reveal());
    $container->addExpressionObject($this->falseConditionExpression->reveal());

    // Delete the first condition.
    $uuid = $this->trueConditionExpression->reveal()->getUuid();
    $this->assertTrue($container->deleteExpression($uuid));
    foreach ($container as $condition) {
      $this->assertSame($this->falseConditionExpression->reveal(), $condition);
    }

    $this->assertFalse($container->deleteExpression('invalid UUID'));
  }

  /**
   * Tests deleting a nested condition from the container.
   */
  public function testDeletingNestedCondition(): void {
    $container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      $this->prophesize(LoggerChannelInterface::class)->reveal(),
    ], '', TRUE);
    $container->addExpressionObject($this->trueConditionExpression->reveal());

    $nested_container = $this->getMockForAbstractClass(RulesConditionContainerTestStub::class, [
      [],
      'test_id',
      [],
      $this->prophesize(ExpressionManagerInterface::class)->reveal(),
      $this->prophesize(LoggerChannelInterface::class)->reveal(),
    ], '', TRUE);
    $nested_container->addExpressionObject($this->falseConditionExpression->reveal());

    $container->addExpressionObject($nested_container);

    $uuid = $this->falseConditionExpression->reveal()->getUuid();
    $this->assertTrue($container->deleteExpression($uuid));
    $this->assertCount(0, $nested_container->getIterator());
  }

}

/**
 * Class used for overriding evaluate() as this does not work with PHPunit.
 */
abstract class RulesConditionContainerTestStub extends ConditionExpressionContainer {

  /**
   * {@inheritdoc}
   */
  public function evaluate(ExecutionStateInterface $state): bool {
    return TRUE;
  }

}
