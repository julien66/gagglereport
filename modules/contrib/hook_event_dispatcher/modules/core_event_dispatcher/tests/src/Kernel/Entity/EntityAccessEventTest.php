<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\Core\Access\AccessResultAllowed;
use Drupal\Core\Access\AccessResultForbidden;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Access\AccessResultNeutral;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityAccessEvent;
use Drupal\KernelTests\KernelTestBase;

/**
 * Class EntityAccessEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityAccessEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityAccessEventTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'entity_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Data provider for testEntityAccessEvent.
   *
   * @return array[]
   *   The provided data.
   *
   * @see \Drupal\Tests\core_event_dispatcher\Kernel\Entity\EntityAccessEventTest::testEntityAccessEvent()
   */
  public static function entityAccessEventDataProvider(): array {
    return [
      'neutral access result' => [
        new AccessResultNeutral(),
        TRUE,
        FALSE,
        FALSE,
      ],
      'allowed access result' => [
        new AccessResultAllowed(),
        FALSE,
        TRUE,
        FALSE,
      ],
      'forbidden access result' => [
        new AccessResultForbidden(),
        FALSE,
        FALSE,
        TRUE,
      ],
    ];
  }

  /**
   * EntityAccessEvent with no changes test.
   *
   * @throws \Exception
   */
  public function testEntityAccessEventWithNoChanges(): void {
    $entityTypeManager = $this->container->get('entity_type.manager');

    $entity = $entityTypeManager->getStorage('entity_test')->create(['name' => $this->randomMachineName()]);
    $user = $entityTypeManager->getStorage('user')->create();
    $operation = $this->randomString();

    $this->container->get('event_dispatcher')
      ->addListener(EntityHookEvents::ENTITY_ACCESS, function (EntityAccessEvent $event) use ($entity, $operation, $user) {
        $this->assertSame($entity, $event->getEntity());
        $this->assertSame($operation, $event->getOperation());
        $this->assertSame($user, $event->getAccount());
      });

    $this->assertFalse($entity->access($operation, $user));
  }

  /**
   * Test EntityAccessEvent.
   *
   * @dataProvider entityAccessEventDataProvider
   *
   * @throws \Exception
   */
  public function testEntityAccessEvent(AccessResultInterface $accessResult, bool $isNeutral, bool $isAllowed, bool $isForbidden): void {
    $this->container->get('event_dispatcher')
      ->addListener(EntityHookEvents::ENTITY_ACCESS, static function (EntityAccessEvent $event) use ($accessResult) {
        $event->addAccessResult($accessResult);
      });

    $entity = $this->container->get('entity_type.manager')
      ->getStorage('entity_test')
      ->create(['name' => $this->randomMachineName()]);

    $access = $entity->access($this->randomString(), NULL, TRUE);

    $this->assertEquals($isNeutral, $access->isNeutral());
    $this->assertEquals($isAllowed, $access->isAllowed());
    $this->assertEquals($isForbidden, $access->isForbidden());
  }

  /**
   * EntityAccessEvent with combined results test.
   *
   * This simulates multiple event listeners adding their own access results to
   * this event.
   *
   * @throws \Exception
   */
  public function testEntityAccessEventCombinedResults(): void {
    $this->container->get('event_dispatcher')
      ->addListener(EntityHookEvents::ENTITY_ACCESS, static function (EntityAccessEvent $event) {
        $event->addAccessResult(new AccessResultNeutral());
        $event->addAccessResult(new AccessResultAllowed());
        $event->addAccessResult(new AccessResultForbidden());
      });

    $entity = $this->container->get('entity_type.manager')
      ->getStorage('entity_test')
      ->create();

    $access = $entity->access($this->randomString(), NULL, TRUE);

    $this->assertFalse($access->isNeutral());
    $this->assertFalse($access->isAllowed());
    $this->assertTrue($access->isForbidden());
  }

}
