<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityFieldAccessEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityFieldAccessEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityFieldAccessEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityFieldAccessEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * The entity operation.
   *
   * @var string
   */
  protected $operation;

  /**
   * The field definition.
   *
   * @var \Drupal\Core\Field\FieldDefinitionInterface|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $fieldDefinition;

  /**
   * The field item list.
   *
   * @var \Drupal\Core\Field\FieldItemListInterface<\Drupal\Core\Field\FieldItemInterface>|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $fieldItemList;

  /**
   * The access result.
   *
   * @var \Drupal\Core\Access\AccessResultInterface
   */
  protected $accessResult;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->operation = $this->randomMachineName();
    $this->fieldDefinition = $this->createMock(FieldDefinitionInterface::class);

    $this->fieldItemList = $this->createMock(FieldItemListInterface::class);
    $this->fieldItemList->method('defaultAccess')
      ->willReturn(AccessResult::neutral());
  }

  /**
   * Field access event without result set.
   *
   * @throws \Exception
   */
  public function testEntityFieldAccessEventWithoutResult(): void {
    $this->listen(EntityHookEvents::ENTITY_FIELD_ACCESS, 'onEntityFieldAccessEventWithoutResult');

    $accessControlHandler = $this->container->get('entity_type.manager')
      ->getAccessControlHandler('entity_test');
    $access = $accessControlHandler->fieldAccess($this->operation, $this->fieldDefinition, NULL, $this->fieldItemList, TRUE);
    $this->assertInstanceOf(AccessResultInterface::class, $access);
    $this->assertTrue($access->isNeutral());
  }

  /**
   * Callback for EntityFieldAccessEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityFieldAccessEvent $event
   *   The event.
   */
  public function onEntityFieldAccessEventWithoutResult(EntityFieldAccessEvent $event): void {
    $this->assertEquals($this->operation, $event->getOperation());
    $this->assertSame($this->fieldDefinition, $event->getFieldDefinition());
    $this->assertTrue($event->getAccount()->isAnonymous());
    $this->assertSame($this->fieldItemList, $event->getItems());
  }

  /**
   * Field access event with result set.
   *
   * @dataProvider entityFieldAccessEventWithResultSetProvider
   *
   * @throws \Exception
   */
  public function testEntityFieldAccessEventWithResultSet(AccessResultInterface $accessResult): void {
    $this->listen(EntityHookEvents::ENTITY_FIELD_ACCESS, 'onEntityFieldAccessEventWithResultSet');

    $this->accessResult = $accessResult;

    $accessControlHandler = $this->container->get('entity_type.manager')
      ->getAccessControlHandler('entity_test');
    $access = $accessControlHandler->fieldAccess($this->operation, $this->fieldDefinition, NULL, $this->fieldItemList, TRUE);
    $this->assertInstanceOf(AccessResultInterface::class, $access);
    $this->assertEquals($accessResult->isNeutral(), $access->isNeutral());
    $this->assertEquals($accessResult->isAllowed(), $access->isAllowed());
    $this->assertEquals($accessResult->isForbidden(), $access->isForbidden());
  }

  /**
   * Callback for EntityFieldAccessEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityFieldAccessEvent $event
   *   The event.
   */
  public function onEntityFieldAccessEventWithResultSet(EntityFieldAccessEvent $event): void {
    $event->setAccessResult($this->accessResult);
  }

  /**
   * Data provider for testEntityFieldAccessEventWithResultSet.
   *
   * @return array[]
   *   The provided data.
   *
   * @see \Drupal\Tests\core_event_dispatcher\Kernel\Entity\EntityFieldAccessEventTest::testEntityFieldAccessEventWithResultSet()
   */
  public static function entityFieldAccessEventWithResultSetProvider(): array {
    return [
      'allowed access result' => [AccessResult::allowed()],
      'forbidden access result' => [AccessResult::forbidden()],
    ];
  }

  /**
   * Field access event with no items.
   *
   * @throws \Exception
   */
  public function testEntityFieldAccessEventWithNoItems(): void {
    $this->listen(EntityHookEvents::ENTITY_FIELD_ACCESS, 'onEntityFieldAccessEventWithNoItems');

    $accessControlHandler = $this->container->get('entity_type.manager')
      ->getAccessControlHandler('entity_test');
    $accessControlHandler->fieldAccess($this->operation, $this->fieldDefinition, NULL, NULL, TRUE);

  }

  /**
   * Callback for EntityFieldAccessEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityFieldAccessEvent $event
   *   The event.
   */
  public function onEntityFieldAccessEventWithNoItems(EntityFieldAccessEvent $event): void {
    $this->assertNull($event->getItems());
  }

}
