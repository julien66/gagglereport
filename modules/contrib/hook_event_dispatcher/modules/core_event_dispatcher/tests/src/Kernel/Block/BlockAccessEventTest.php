<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Access\AccessResultInterface;
use Drupal\core_event_dispatcher\BlockHookEvents;
use Drupal\core_event_dispatcher\Event\Block\BlockAccessEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class BlockAccessEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Block\BlockAccessEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class BlockAccessEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'user',
    'block',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * The access result.
   *
   * @var \Drupal\Core\Access\AccessResultInterface
   */
  protected $accessResult;

  /**
   * The block instance.
   *
   * @var \Drupal\block\BlockInterface
   */
  protected $block;

  /**
   * The operation to be performed.
   *
   * @var string
   */
  protected $operation;

  /**
   * Test BlockAccessEvent.
   *
   * @dataProvider blockAccessProvider
   *
   * @throws \Exception
   */
  public function testBlockAccessEvent(AccessResultInterface $accessResult, bool $expected): void {
    $this->listen(BlockHookEvents::BLOCK_ACCESS, 'onBlockAccess');

    $this->accessResult = $accessResult;

    $this->block = $this->container->get('entity_type.manager')->getStorage('block')->create();
    $this->operation = $this->randomMachineName();

    $this->assertEquals($expected, $this->block->access($this->operation));
  }

  /**
   * Callback for BlockAccessEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Block\BlockAccessEvent $event
   *   The event.
   */
  public function onBlockAccess(BlockAccessEvent $event): void {
    $this->assertSame($this->block, $event->getBlock());
    $this->assertEquals($this->operation, $event->getOperation());
    $this->assertTrue($event->getAccount()->isAnonymous());

    $event->setAccessResult($this->accessResult);
  }

  /**
   * Data provider for testBlockAccessEvent.
   *
   * @return array[]
   *   The provided data.
   *
   * @see \Drupal\Tests\core_event_dispatcher\Kernel\Block\BlockAccessEventTest::testBlockAccessEvent()
   */
  public static function blockAccessProvider(): array {
    return [
      'allowed access result' => [AccessResult::allowed(), TRUE],
      'neutral access result' => [AccessResult::neutral(), FALSE],
      'forbidden access result' => [AccessResult::forbidden(), FALSE],
    ];
  }

}
