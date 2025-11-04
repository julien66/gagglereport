<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Entity;

use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoAlterEvent;
use Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class EntityExtraFieldInfoEventTest.
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class EntityExtraFieldInfoEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * The entity bundle.
   *
   * @var string
   */
  protected $bundle;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();

    $this->bundle = $this->randomMachineName();
    $this->entityFieldManager = $this->container->get('entity_field.manager');
    $this->eventDispatcher = $this->container->get('event_dispatcher');
  }

  /**
   * Data provider for testEntityExtraFieldInfoEvent.
   *
   * @return string[][]
   *   The provided data.
   *
   * @see \Drupal\Tests\core_event_dispatcher\Kernel\Entity\EntityExtraFieldInfoEventTest::testEntityExtraFieldInfoEvent()
   */
  public static function entityExtraFieldInfoEventProvider(): array {
    return [
      'helper functions' => ['onEntityExtraFieldInfoWithHelperFunctions'],
      'set functions' => ['onEntityExtraFieldInfoWithSetFunction'],
    ];
  }

  /**
   * Test EntityExtraFieldInfoEvent.
   *
   * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoEvent
   *
   * @dataProvider entityExtraFieldInfoEventProvider
   *
   * @throws \Exception
   */
  public function testEntityExtraFieldInfoEvent(string $method): void {
    $this->listen(EntityHookEvents::ENTITY_EXTRA_FIELD_INFO, $method);
    $this->assertExtraFields();
  }

  /**
   * Asserts entity extra fields.
   */
  private function assertExtraFields(): void {
    $extraFields = $this->entityFieldManager->getExtraFields('entity_test', $this->bundle);

    foreach (['display', 'form'] as $extra) {
      $this->assertArrayHasKey($extra, $extraFields);
      $this->assertArrayHasKey('field_test', $extraFields[$extra]);
      $this->assertArrayHasKey('test', $extraFields[$extra]['field_test']);
      $this->assertEquals('test', $extraFields[$extra]['field_test']['test']);
    }
  }

  /**
   * Callback for EntityExtraFieldInfoEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoEvent $event
   *   The event.
   */
  public function onEntityExtraFieldInfoWithHelperFunctions(EntityExtraFieldInfoEvent $event): void {
    $event->addDisplayFieldInfo('entity_test', $this->bundle, 'field_test', ['test' => 'test']);
    $event->addFormFieldInfo('entity_test', $this->bundle, 'field_test', ['test' => 'test']);
  }

  /**
   * Callback for EntityExtraFieldInfoEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoEvent $event
   *   The event.
   */
  public function onEntityExtraFieldInfoWithSetFunction(EntityExtraFieldInfoEvent $event): void {
    $event->setFieldInfo([
      'entity_test' => [
        $this->bundle => [
          'display' => [
            'field_test' => [
              'test' => 'test',
            ],
          ],
          'form' => [
            'field_test' => [
              'test' => 'test',
            ],
          ],
        ],
      ],
    ]);
  }

  /**
   * Test EntityExtraFieldInfoAlterEvent.
   *
   * @covers \Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoAlterEvent
   *
   * @throws \Exception
   */
  public function testEntityExtraFieldInfoAlterEvent(): void {
    $this->listen(EntityHookEvents::ENTITY_EXTRA_FIELD_INFO_ALTER, 'onEntityExtraFieldInfoAlterEvent');
    $this->assertExtraFields();
  }

  /**
   * Callback for EntityExtraFieldInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Entity\EntityExtraFieldInfoAlterEvent $event
   *   The event.
   */
  public function onEntityExtraFieldInfoAlterEvent(EntityExtraFieldInfoAlterEvent $event): void {
    $fields = &$event->getFieldInfo();
    $fields['entity_test'] = [
      $this->bundle => [
        'display' => [
          'field_test' => [
            'test' => 'test',
          ],
        ],
        'form' => [
          'field_test' => [
            'test' => 'test',
          ],
        ],
      ],
    ];
  }

}
