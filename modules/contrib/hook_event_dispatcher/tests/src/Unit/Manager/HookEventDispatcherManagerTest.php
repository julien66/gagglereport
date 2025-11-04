<?php

namespace Drupal\Tests\hook_event_dispatcher\Unit\Manager;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Manager\HookEventDispatcherManager;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class HookEventDispatcherManagerTest.
 *
 * @coversDefaultClass \Drupal\hook_event_dispatcher\Manager\HookEventDispatcherManager
 *
 * @package Drupal\Tests\hook_event_dispatcher\Unit\Manager
 *
 * @group hook_event_dispatcher
 */
class HookEventDispatcherManagerTest extends TestCase {

  /**
   * Test event dispatcher.
   *
   * @covers ::__construct
   * @covers ::register
   *
   * @dataProvider eventDispatcherDataProvider
   */
  public function testEventDispatcher(bool $isPropagationStopped): void {
    $event = new class('test') extends Event implements EventInterface {

      public function __construct(private readonly string $dispatcherType) {}

      public function getDispatcherType(): string {
        return $this->dispatcherType;
      }

    };
    !$isPropagationStopped ?: $event->stopPropagation();
    $dispatcher = $this->createMock(EventDispatcherInterface::class);
    $dispatcher->method('dispatch')->with($event, 'test')->willReturn($event);

    $manager = new HookEventDispatcherManager($dispatcher);
    $returnedEvent = $manager->register($event);
    self::assertSame($event, $returnedEvent);
  }

  public static function eventDispatcherDataProvider(): \Generator {
    yield [FALSE];
    yield [TRUE];
  }

}
