<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\Condition;

use Drupal\Tests\rules\Unit\Integration\RulesEntityIntegrationTestBase;
use Drupal\user\UserInterface;

/**
 * @coversDefaultClass \Drupal\rules\Plugin\Condition\UserIsBlocked
 * @group RulesCondition
 */
class UserIsBlockedTest extends RulesEntityIntegrationTestBase {

  /**
   * The condition to be tested.
   *
   * @var \Drupal\rules\Core\RulesConditionInterface
   */
  protected $condition;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->enableModule('user');
    $this->condition = $this->conditionManager->createInstance('rules_user_is_blocked');
  }

  /**
   * Tests evaluating the condition.
   *
   * @covers ::evaluate
   */
  public function testConditionEvaluation(): void {
    $blocked_user = $this->prophesizeEntity(UserInterface::class);
    $blocked_user->isBlocked()->willReturn(TRUE)->shouldBeCalledTimes(1);

    // Set the user context value.
    $this->condition->setContextValue('user', $blocked_user->reveal());

    $this->assertTrue($this->condition->evaluate());

    $active_user = $this->prophesizeEntity(UserInterface::class);
    $active_user->isBlocked()->willReturn(FALSE)->shouldBeCalledTimes(1);

    // Set the user context value.
    $this->condition->setContextValue('user', $active_user->reveal());

    $this->assertFalse($this->condition->evaluate());
  }

}
