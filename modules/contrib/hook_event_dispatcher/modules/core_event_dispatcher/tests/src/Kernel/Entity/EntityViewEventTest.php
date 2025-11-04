<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityBuildDefaultsAlterEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityViewAlterEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityViewEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityViewEventTest.
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityViewEventTest extends KernelTestBase {

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
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The entity view builder handler.
   *
   * @var \Drupal\Core\Entity\EntityViewBuilder
   */
  protected EntityViewBuilder $viewBuilder;

  /**
   * The entity test.
   *
   * @var \Drupal\Core\Entity\EntityInterface
   */
  protected EntityInterface $entity;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->entity = $this->entityTypeManager->getStorage('entity_test')
      ->create();

    $entityType = $this->entityTypeManager->getDefinition('entity_test');
    /** @var class-string<\Drupal\Core\Entity\EntityViewBuilder> $handlerClass */
    $handlerClass = $entityType->getHandlerClass('view_builder');
    $this->viewBuilder = $handlerClass::createInstance($this->container, $entityType);
  }

  /**
   * Test EntityViewEvent.
   *
   * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityViewAlterEvent
   * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityViewEvent
   *
   * @throws \Exception
   */
  public function testEntityViewEventByReference(): void {
    $this->listen(EntityHookEvents::ENTITY_VIEW, 'onEntityViewEvent');
    $this->listen(EntityHookEvents::ENTITY_VIEW_ALTER, 'onEntityViewAlterEvent');

    $build = $this->viewBuilder->buildMultiple([
      [
        '#entity_test' => $this->entity,
        '#view_mode' => 'entity_view_mode',
      ],
    ]);

    $this->assertArrayHasKey(0, $build);
    $this->assertArrayHasKey('otherBuild', $build[0]);
    $this->assertCount(2, $build[0]['otherBuild']);
  }

  /**
   * Callback for EntityViewEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityViewEvent $event
   *   The event.
   */
  public function onEntityViewEvent(EntityViewEvent $event): void {
    $this->assertEquals('entity_test.entity_test.entity_view_mode', $event->getDisplay()
      ->id());
    $this->assertEquals('entity_view_mode', $event->getViewMode());

    $build = &$event->getBuild();
    $build['otherBuild'] = [$this->randomMachineName()];
  }

  /**
   * Callback for EntityViewAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityViewAlterEvent $event
   *   The event.
   */
  public function onEntityViewAlterEvent(EntityViewAlterEvent $event): void {
    $this->assertEquals('entity_test.entity_test.entity_view_mode', $event->getDisplay()
      ->id());

    $build = &$event->getBuild();
    $build['otherBuild'][] = $this->randomMachineName();
  }

  /**
   * Test EntityBuildDefaultsAlter.
   *
   * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityBuildDefaultsAlterEvent
   *
   * @throws \Exception
   */
  public function testEntityBuildDefaultsAlterEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_BUILD_DEFAULTS_ALTER, 'onEntityBuildDefaultsAlter');

    $build = $this->viewBuilder->viewMultiple([$this->entity]);
    $this->assertArrayHasKey(0, $build);
    $this->assertArrayHasKey('otherBuild', $build[0]);
    $this->assertCount(1, $build[0]['otherBuild']);
  }

  /**
   * Callback for EntityBuildDefaultAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityBuildDefaultsAlterEvent $event
   *   The event.
   */
  public function onEntityBuildDefaultsAlter(EntityBuildDefaultsAlterEvent $event): void {
    $this->assertEquals('full', $event->getViewMode());

    $build = &$event->getBuild();
    $build['otherBuild'] = [$this->randomMachineName()];
  }

}
