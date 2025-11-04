<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\RulesAction;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Tests\rules\Unit\Integration\RulesEntityIntegrationTestBase;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\RulesAction\EntityDelete
 * @group RulesAction
 */
class EntityDeleteTest extends RulesEntityIntegrationTestBase {

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

    $this->action = $this->actionManager->createInstance('rules_entity_delete');
  }

  /**
   * Tests the summary.
   *
   * @covers ::summary
   */
  public function testSummary(): void {
    $this->assertEquals('Delete entity', $this->action->summary());
  }

  /**
   * Tests the action execution.
   *
   * @covers ::execute
   */
  public function testActionExecution(): void {
    $entity = $this->prophesizeEntity(EntityInterface::class);
    $entity->delete()->shouldBeCalledTimes(1);

    $this->action->setContextValue('entity', $entity->reveal());
    $this->action->execute();
  }

}
