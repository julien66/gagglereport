<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityCreateAccessEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityCreateAccessEvent.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityCreateAccessEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityCreateAccessEventTest extends KernelTestBase {

  use ListenerTrait;

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
   * The access result.
   *
   * @var \Drupal\Core\Access\AccessResultInterface
   */
  protected AccessResultInterface $accessResult;

  /**
   * Test EntityCreateAccessEvent.
   *
   * @dataProvider entityCreateAccessEventProvider
   *
   * @throws \Exception
   */
  public function testEntityCreateAccessEvent(AccessResultInterface $accessResult, bool $access): void {
    $this->listen(EntityHookEvents::ENTITY_CREATE_ACCESS, 'onEntityCreateAccess');

    $this->accessResult = $accessResult;

    $accessControlHandler = $this->container->get('entity_type.manager')->getAccessControlHandler('entity_test');
    $context = [
      'test' => TRUE,
    ];

    $this->assertEquals($access, $accessControlHandler->createAccess('test_bundle', NULL, $context));
  }

  /**
   * Callback for EntityCreateAccessEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityCreateAccessEvent $event
   *   The event.
   */
  public function onEntityCreateAccess(EntityCreateAccessEvent $event): void {
    $context = $event->getContext();
    $this->assertNotEmpty($context['test']);
    $this->assertEquals('test_bundle', $event->getEntityBundle());

    $event->addAccessResult($this->accessResult);
  }

  /**
   * Data provider for testEntityCreateAccessEvent.
   */
  public static function entityCreateAccessEventProvider(): array {
    return [
      [AccessResult::allowed(), TRUE],
      [AccessResult::forbidden(), FALSE],
      [AccessResult::neutral(), FALSE],
    ];
  }

  /**
   * Test EntityCreateAccessEvent for integer bundle.
   *
   * @throws \Exception
   */
  public function testEntityCreateAccessEventIntegerBundle(): void {
    $bundle = mt_rand();
    entity_test_create_bundle($bundle);

    $accessControlHandler = $this->container->get('entity_type.manager')->getAccessControlHandler('entity_test');
    $context = [
      'test' => TRUE,
    ];
    $this->assertFalse($accessControlHandler->createAccess($bundle, NULL, $context));
  }

}
