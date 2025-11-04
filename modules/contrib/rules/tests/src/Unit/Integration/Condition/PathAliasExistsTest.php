<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\Condition;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Tests\rules\Unit\Integration\RulesIntegrationTestBase;
use Drupal\path_alias\AliasManagerInterface;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Condition\PathAliasExists
 * @group RulesCondition
 */
class PathAliasExistsTest extends RulesIntegrationTestBase {

  /**
   * The condition to be tested.
   *
   * @var \Drupal\rules\Core\RulesConditionInterface
   */
  protected $condition;

  /**
   * @var \Drupal\path_alias\AliasManagerInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $aliasManager;

  /**
   * A mocked language object (english).
   *
   * @var \Drupal\Core\Language\LanguageInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $englishLanguage;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Must enable the path_alias module.
    $this->enableModule('path_alias');
    $this->aliasManager = $this->prophesize(AliasManagerInterface::class);
    $this->container->set('path_alias.manager', $this->aliasManager->reveal());

    $this->condition = $this->conditionManager->createInstance('rules_path_alias_exists');

    $this->englishLanguage = $this->prophesize(LanguageInterface::class);
    $this->englishLanguage->getId()->willReturn('en');
  }

  /**
   * Tests that the dependencies are properly set in the constructor.
   *
   * @covers ::__construct
   */
  public function testConstructor(): void {
    $property = new \ReflectionProperty($this->condition, 'aliasManager');
    $property->setAccessible(TRUE);

    $this->assertSame($this->aliasManager->reveal(), $property->getValue($this->condition));
  }

  /**
   * Tests evaluating the condition for an alias that can be resolved.
   *
   * @covers ::evaluate
   */
  public function testConditionEvaluationAliasWithPath(): void {
    // If the path exists, getPathByAlias() should return the path.
    $this->aliasManager->getPathByAlias('/alias-for-path', NULL)
      ->willReturn('/path-with-alias')
      ->shouldBeCalledTimes(1);

    $this->aliasManager->getPathByAlias('/alias-for-path', 'en')
      ->willReturn('/path-with-alias')
      ->shouldBeCalledTimes(1);

    // First, only set the path context.
    $this->condition->setContextValue('alias', '/alias-for-path');

    // Test without language context set. This should return true because the
    // alias is defined.
    $this->assertTrue($this->condition->evaluate());

    // Test with language context set. Again, this should return true because
    // the alias is defined.
    $this->condition->setContextValue('language', $this->englishLanguage->reveal());
    $this->assertTrue($this->condition->evaluate());
  }

  /**
   * Tests evaluating the condition for an alias that can not be resolved.
   *
   * @covers ::evaluate
   */
  public function testConditionEvaluationAliasWithoutPath(): void {
    // If the path does not exist, getPathByAlias() should return the alias.
    $this->aliasManager->getPathByAlias('/alias-for-path-that-does-not-exist', NULL)
      ->willReturn('/alias-for-path-that-does-not-exist')
      ->shouldBeCalledTimes(1);

    $this->aliasManager->getPathByAlias('/alias-for-path-that-does-not-exist', 'en')
      ->willReturn('/alias-for-path-that-does-not-exist')
      ->shouldBeCalledTimes(1);

    // First, only set the path context.
    $this->condition->setContextValue('alias', '/alias-for-path-that-does-not-exist');

    // Test without language context set. This should return false because the
    // alias was not defined.
    $this->assertFalse($this->condition->evaluate());

    // Test with language context set.
    $this->condition->setContextValue('language', $this->englishLanguage->reveal());
    $this->assertFalse($this->condition->evaluate());
  }

}
