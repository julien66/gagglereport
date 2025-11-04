<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Core\TypedData\TypedDataInterface;
use Drupal\Tests\rules\Unit\Integration\RulesIntegrationTestBase;
use Drupal\rules\Exception\InvalidArgumentException;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\DataConvert
 * @group RulesAction
 */
class DataConvertTest extends RulesIntegrationTestBase {

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

    $this->action = $this->actionManager->createInstance('rules_data_convert');
  }

  /**
   * Test the conversion and rounding to integer.
   *
   * @covers ::execute
   */
  public function testConvertToInteger(): void {
    $value = 1.5;

    // Test the conversion to integer.
    $converted = $this->executeAction($value, 'integer');
    $this->assertIsInt($converted->getValue());
    $this->assertEquals('integer', $converted->getDataDefinition()->getDataType());

    // Test the conversion to integer and floor down.
    $converted = $this->executeAction($value, 'integer', 'down');
    $this->assertIsInt($converted->getValue());
    $this->assertEquals(1, $converted->getValue());
    $this->assertEquals('integer', $converted->getDataDefinition()->getDataType());

    // Test the conversion to integer and ceil up.
    $converted = $this->executeAction($value, 'integer', 'up');
    $this->assertIsInt($converted->getValue());
    $this->assertEquals('integer', $converted->getDataDefinition()->getDataType());
    $this->assertEquals(2, $converted->getValue());

    // Test the conversion to integer and round.
    $converted = $this->executeAction($value, 'integer', 'round');
    $this->assertIsInt($converted->getValue());
    $this->assertEquals('integer', $converted->getDataDefinition()->getDataType());
    $this->assertEquals(2, $converted->getValue());

    $converted = $this->executeAction('+123', 'integer');
    $this->assertIsInt($converted->getValue());
    $this->assertEquals('integer', $converted->getDataDefinition()->getDataType());
    $this->assertEquals(123, $converted->getValue());
  }

  /**
   * Test the conversion to float.
   *
   * @covers ::execute
   */
  public function testConvertToFloat(): void {
    $value = '1.5';

    $converted = $this->executeAction($value, 'float');
    $this->assertIsFloat($converted->getValue());
    $this->assertEquals('float', $converted->getDataDefinition()->getDataType());
    $this->assertEquals(1.5, $converted->getValue());

    $converted = $this->executeAction('+1.5', 'float');
    $this->assertIsFloat($converted->getValue());
    $this->assertEquals('float', $converted->getDataDefinition()->getDataType());
    $this->assertEquals(1.5, $converted->getValue());
  }

  /**
   * Test the conversion to text.
   *
   * @covers ::execute
   */
  public function testConvertToString(): void {
    // Test the conversion to test/string.
    $value = 1.5;

    $converted = $this->executeAction($value, 'string');
    $this->assertIsString($converted->getValue());
    $this->assertEquals('string', $converted->getDataDefinition()->getDataType());
    $this->assertEquals('1.5', $converted->getValue());
  }

  /**
   * Test the behavior if nonsense context values is set.
   *
   * @covers ::execute
   */
  public function testInvalidValueException(): void {
    // Set the expected exception class and message.
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Only scalar values are supported.');

    $this->executeAction(['some-array'], 'integer');
  }

  /**
   * Test the behavior if rounding behavior is used with non integers.
   *
   * @covers ::execute
   */
  public function testInvalidRoundingBehavior(): void {
    // Set the expected exception class and message.
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('A rounding behavior only makes sense with an integer target type.');

    $converted = $this->executeAction('some', 'decimal', 'down');
    $this->assertIsFloat($converted->getValue());
    $this->assertEquals('float', $converted->getDataDefinition()->getDataType());
  }

  /**
   * Test the behavior if nonsense rounding_behaviors is set.
   *
   * @covers ::execute
   */
  public function testInvalidRoundingBehaviorException(): void {
    // Set the expected exception class and message.
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Unknown rounding behavior: invalid rounding');

    $value = 5.5;
    $rounding_behavior = 'invalid rounding';
    $this->executeAction($value, 'integer', $rounding_behavior);
  }

  /**
   * Test the behavior if nonsense target_type is set.
   *
   * @covers ::execute
   */
  public function testInvalidTargetTypeException(): void {
    // Set the expected exception class and message.
    $this->expectException(InvalidArgumentException::class);
    $this->expectExceptionMessage('Unknown target type: invalid type');
    $value = 5.5;
    $target_type = 'invalid type';
    $this->executeAction($value, $target_type);
  }

  /**
   * Test that the provided context variable is the correct type.
   *
   * @covers ::refineContextDefinitions
   */
  public function testRefiningContextDefinitions(): void {
    // Before context refinement, conversion_result data type defaults to 'any'.
    $this->assertEquals(
      'any',
      $this->action->getProvidedContextDefinition('conversion_result')->getDataType()
    );
    $this->action->setContextValue('target_type', 'date_iso8601');
    $this->action->refineContextDefinitions([]);
    // After context refinement, data type is whatever we set target_type to.
    $this->assertEquals(
      'date_iso8601',
      $this->action->getProvidedContextDefinition('conversion_result')->getDataType()
    );
  }

  /**
   * Shortcut method to execute the convert action and to avoid duplicate code.
   *
   * @param mixed $value
   *   The value to be converted.
   * @param string $target_type
   *   The target type $value should be converted to.
   * @param null|string $rounding_behavior
   *   Definition for the rounding direction.
   *
   * @return \Drupal\Core\TypedData\TypedDataInterface
   *   The raw conversion result as a typed data object.
   */
  protected function executeAction($value, $target_type, $rounding_behavior = NULL): TypedDataInterface {

    $this->action->setContextValue('value', $value);
    $this->action->setContextValue('target_type', $target_type);

    if (!empty($rounding_behavior)) {
      $this->action->setContextValue('rounding_behavior', $rounding_behavior);
    }

    $this->action->refineContextDefinitions([]);
    $this->action->execute();
    $result = $this->action->getProvidedContext('conversion_result');
    return $result->getContextData();
  }

}
