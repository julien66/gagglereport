<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\Core\Entity\EntityListBuilderInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityOperationAlterEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityOperationEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityOperationEventTest.
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityOperationEventTest extends KernelTestBase {

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
   * The entity operations.
   *
   * @var string[][]
   */
  protected $operations = [
    'test' => [
      'title' => 'new',
    ],
  ];

  /**
   * The extra operations.
   *
   * @var string[]
   */
  protected $extraOperation = [
    'title' => 'extra',
  ];

  /**
   * The entity.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected $entity;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entity = $this->container->get('entity_type.manager')
      ->getStorage('entity_test')
      ->create([]);
  }

  /**
   * EntityOperationEvent test.
   *
   * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityOperationEvent
   *
   * @throws \Exception
   */
  public function testEntityOperationEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_OPERATION, 'onEntityOperation');

    $listBuilder = $this->container->get('entity_type.manager')->getHandler('entity_test', 'list_builder');
    $this->assertInstanceOf(EntityListBuilderInterface::class, $listBuilder);
    $operations = $listBuilder->getOperations($this->entity);
    $this->assertEquals($operations + ['extra' => $this->extraOperation], $operations);
  }

  /**
   * Callback for EntityOperationEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityOperationEvent $event
   *   The event.
   */
  public function onEntityOperation(EntityOperationEvent $event): void {
    $this->assertSame($this->entity, $event->getEntity());
    $event->setOperations($this->operations);
    $event->addOperation('extra', $this->extraOperation);
  }

  /**
   * EntityOperationAlterEvent test.
   *
   * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityOperationAlterEvent
   *
   * @throws \Exception
   */
  public function testEntityOperationAlterEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_OPERATION_ALTER, 'onEntityOperationAlter');

    $listBuilder = $this->container->get('entity_type.manager')->getHandler('entity_test', 'list_builder');
    $this->assertInstanceOf(EntityListBuilderInterface::class, $listBuilder);
    $operations = $listBuilder->getOperations($this->entity);
    $this->assertEquals($operations + ['extra' => $this->extraOperation], $operations);
  }

  /**
   * Callback for EntityOperationAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityOperationAlterEvent $event
   *   The event.
   */
  public function onEntityOperationAlter(EntityOperationAlterEvent $event): void {
    $this->assertSame($this->entity, $event->getEntity());
    $operations = &$event->getOperations();
    $operations = array_merge($operations, $this->operations, ['extra' => $this->extraOperation]);
  }

}
