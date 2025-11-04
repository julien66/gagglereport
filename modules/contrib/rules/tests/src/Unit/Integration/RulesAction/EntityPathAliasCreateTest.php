<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Url;
use Drupal\Tests\rules\Unit\Integration\RulesEntityIntegrationTestBase;
use Drupal\path_alias\PathAliasInterface;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\EntityPathAliasCreate
 * @group RulesAction
 */
class EntityPathAliasCreateTest extends RulesEntityIntegrationTestBase {

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

    // Instantiate the action we are testing.
    $this->action = $this->actionManager->createInstance('rules_entity_path_alias_create:entity:test');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary(): void {
    $this->assertEquals('Create a test path alias', $this->action->summary());
  }

  /**
   * Tests the action execution with an unsaved entity.
   *
   * @covers ::execute
   */
  public function testActionExecutionWithUnsavedEntity(): void {
    $path_alias = $this->prophesizeEntity(PathAliasInterface::class);

    // Test that the alias is only saved once.
    $path_alias->save()->shouldBeCalledTimes(1);

    $this->aliasStorage->create([
      'path' => '/test/1',
      'alias' => '/about',
      'langcode' => 'en',
    ])->willReturn($path_alias->reveal())
      ->shouldBeCalledTimes(1);

    $entity = $this->getMockEntity();
    $entity->isNew()->willReturn(TRUE)->shouldBeCalledTimes(1);

    // Test that new entities are saved first.
    $entity->save()->shouldBeCalledTimes(1);

    $this->action->setContextValue('entity', $entity->reveal())
      ->setContextValue('alias', '/about');

    $this->action->execute();
  }

  /**
   * Tests the action execution with a saved entity.
   *
   * @covers ::execute
   */
  public function testActionExecutionWithSavedEntity(): void {
    $path_alias = $this->prophesizeEntity(PathAliasInterface::class);

    // Test that the alias is only saved once.
    $path_alias->save()->shouldBeCalledTimes(1);

    $this->aliasStorage->create([
      'path' => '/test/1',
      'alias' => '/about',
      'langcode' => 'en',
    ])->willReturn($path_alias->reveal())
      ->shouldBeCalledTimes(1);

    $entity = $this->getMockEntity();
    $entity->isNew()->willReturn(FALSE)->shouldBeCalledTimes(1);

    // Test that existing entities are not saved again.
    $entity->save()->shouldNotBeCalled();

    $this->action->setContextValue('entity', $entity->reveal())
      ->setContextValue('alias', '/about');

    $this->action->execute();
  }

  /**
   * Creates a mock entity.
   *
   * @return \Drupal\Core\Entity\EntityInterface|\Prophecy\Prophecy\ProphecyInterface
   *   The mocked entity object.
   */
  protected function getMockEntity() {
    $language = $this->languageManager->reveal()->getCurrentLanguage();

    $entity = $this->prophesizeEntity(EntityInterface::class);
    $entity->language()->willReturn($language)->shouldBeCalledTimes(1);

    $url = $this->prophesize(Url::class);
    $url->getInternalPath()->willReturn('test/1')->shouldBeCalledTimes(1);

    $entity->toUrl(Argument::any())->willReturn($url->reveal())
      ->shouldBeCalledTimes(1);

    return $entity;
  }

}
