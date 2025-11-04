<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Tests\rules\Unit\Integration\RulesEntityIntegrationTestBase;
use Drupal\path_alias\PathAliasInterface;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\PathAliasCreate
 * @group RulesAction
 */
class PathAliasCreateTest extends RulesEntityIntegrationTestBase {

  /**
   * The action to be tested.
   *
   * @var \Drupal\rules\Core\RulesActionInterface
   */
  protected $action;

  /**
   * The mocked alias storage service.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface|\Prophecy\Prophecy\ProphecyInterface
   */
  protected $aliasStorage;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Must enable the path_alias module.
    $this->enableModule('path_alias');

    // Prepare mocked EntityStorageInterface.
    $this->aliasStorage = $this->prophesize(EntityStorageInterface::class);
    $this->entityTypeManager->getStorage('path_alias')->willReturn($this->aliasStorage->reveal());

    $this->action = $this->actionManager->createInstance('rules_path_alias_create');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary(): void {
    $this->assertEquals('Create any path alias', $this->action->summary());
  }

  /**
   * Tests the action execution when no language is specified.
   *
   * @covers ::execute
   */
  public function testActionExecutionWithoutLanguage(): void {
    $path_alias = $this->prophesizeEntity(PathAliasInterface::class);
    $path_alias->save()->shouldBeCalledTimes(1);

    $this->aliasStorage->create([
      'path' => '/node/1',
      'alias' => '/about',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
    ])->willReturn($path_alias->reveal())
      ->shouldBeCalledTimes(1);

    $this->action->setContextValue('source', '/node/1')
      ->setContextValue('alias', '/about');

    $this->action->execute();
  }

  /**
   * Tests the action execution when a language is specified.
   *
   * @covers ::execute
   */
  public function testActionExecutionWithLanguage(): void {
    $path_alias = $this->prophesizeEntity(PathAliasInterface::class);
    $path_alias->save()->shouldBeCalledTimes(1);

    $language = $this->prophesize(LanguageInterface::class);
    $language->getId()->willReturn('en');

    $this->aliasStorage->create([
      'path' => '/node/1',
      'alias' => '/about',
      'langcode' => 'en',
    ])->willReturn($path_alias->reveal())
      ->shouldBeCalledTimes(1);

    $this->action->setContextValue('source', '/node/1')
      ->setContextValue('alias', '/about')
      ->setContextValue('language', $language->reveal());

    $this->action->execute();
  }

}
