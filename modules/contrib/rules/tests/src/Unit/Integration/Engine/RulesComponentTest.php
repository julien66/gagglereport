<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\Engine;

use Drupal\rules\Context\ContextConfig;
use Drupal\rules\Context\ContextDefinition;
use Drupal\rules\Engine\RulesComponent;
use Drupal\rules\Context\ExecutionStateInterface;
use Drupal\Tests\rules\Unit\Integration\RulesIntegrationTestBase;

/**
 * Tests the Rules component class.
 *
 * @coversDefaultClass \Drupal\rules\Engine\RulesComponent
 * @group Rules
 */
class RulesComponentTest extends RulesIntegrationTestBase {

  /**
   * Tests executing a rule providing context based upon given context.
   */
  public function testRuleExecutionWithContext(): void {
    $rule = $this->rulesExpressionManager->createRule();

    $rule->addAction('rules_test_string',
      ContextConfig::create()->map('text', 'text')
    );

    $result = RulesComponent::create($rule)
      ->addContextDefinition('text', ContextDefinition::create('string'))
      ->provideContext('concatenated')
      ->setContextValue('text', 'foo')
      ->execute();

    // Ensure the provided context is returned.
    $this->assertArrayHasKey('concatenated', $result);
    // cspell:ignore foofoo
    $this->assertEquals('foofoo', $result['concatenated']);
  }

  /**
   * @covers ::getExpression
   */
  public function testGetExpression(): void {
    $rule = $this->rulesExpressionManager->createRule();
    $this->assertSame(RulesComponent::create($rule)->getExpression(), $rule);
  }

  /**
   * @covers ::getContextDefinitions
   */
  public function testGetContextDefinitions(): void {
    $rule = $this->rulesExpressionManager->createRule();
    $definition = ContextDefinition::create('string');
    $component = RulesComponent::create($rule)
      ->addContextDefinition('test', $definition);

    $this->assertEquals(array_keys($component->getContextDefinitions()), ['test']);
    $this->assertSame($component->getContextDefinitions()['test'], $definition);
  }

  /**
   * @covers ::getProvidedContext
   */
  public function testGetProvidedContext(): void {
    $rule = $this->rulesExpressionManager->createRule();
    $component = RulesComponent::create($rule)
      ->provideContext('test');

    $this->assertEquals($component->getProvidedContext(), ['test']);
  }

  /**
   * @covers ::getState
   */
  public function testGetState(): void {
    $rule = $this->rulesExpressionManager->createRule();
    $component = RulesComponent::create($rule);
    $this->assertInstanceOf(ExecutionStateInterface::class, $component->getState());

    // Test that set context values are available in the state.
    $component
      ->addContextDefinition('foo', ContextDefinition::create('string'))
      ->setContextValue('foo', 'bar');

    $this->assertEquals($component->getState()->getVariableValue('foo'), 'bar');
  }

}
