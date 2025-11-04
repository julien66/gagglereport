<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Tests\rules\Unit\Integration\RulesIntegrationTestBase;
use Drupal\path_alias\PathAliasInterface;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\PathAliasDeleteByAlias
 * @group RulesAction
 */
class PathAliasDeleteByAliasTest extends RulesIntegrationTestBase {

  /**
   * The action to be tested.
   *
   * @var \Drupal\rules\Core\RulesActionInterface
   */
  protected $action;

  /**
   * The mocked alias storage service.
   *
   * @var \Drupal\Core\Entity\Entity\StorageInterface|\Prophecy\Prophecy\ProphecyInterface
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

    $this->action = $this->actionManager->createInstance('rules_path_alias_delete_by_alias');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary(): void {
    $this->assertEquals('Delete path alias', $this->action->summary());
  }

  /**
   * Tests the action execution.
   *
   * @covers ::execute
   */
  public function testActionExecution(): void {
    $alias = '/about/team';
    $this->action->setContextValue('alias', $alias);

    $path_alias = $this->prophesizeEntity(PathAliasInterface::class);
    $this->aliasStorage->delete([$path_alias->reveal()])->shouldBeCalledTimes(1);

    $this->aliasStorage->loadByProperties(['alias' => $alias])
      ->willReturn([$path_alias->reveal()])
      ->shouldBeCalledTimes(1);

    $this->action->execute();
  }

}
