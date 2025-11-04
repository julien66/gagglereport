<?php

namespace Drupal\Tests\hook_event_dispatcher\Unit\Plugin\Factory;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\hook_event_dispatcher\Event\EventFactoryInterface;
use Drupal\hook_event_dispatcher\Event\EventFactoryTrait;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Plugin\Factory\EventFactory;
use Drupal\Tests\RandomGeneratorTrait;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\hook_event_dispatcher\Plugin\Factory\EventFactory
 */
class EventFactoryTest extends TestCase {

  use RandomGeneratorTrait;

  /**
   * @covers ::createInstance
   *
   * @dataProvider createInstanceProvider
   */
  public function testCreateInstance(string $class): void {
    $discovery = $this->createMock(DiscoveryInterface::class);
    $discovery->method('getDefinition')->willReturn([
      'class' => $class,
    ]);

    $factory = new EventFactory($discovery);
    $instance = $factory->createInstance($this->randomMachineName());
    $this->assertInstanceOf($class, $instance);
  }

  public static function createInstanceProvider(): \Generator {
    $eventFactory = new class() implements EventFactoryInterface {

      use EventFactoryTrait;

      public function __construct() {}

    };

    $event = new class() extends Event implements EventInterface {

      public function getDispatcherType(): string {
        return '';
      }

    };
    yield [$eventFactory::class];
    yield [$event::class];
  }

}

final class StubEventFactory implements EventFactoryInterface {

  use EventFactoryTrait;

  public function __construct() {}

}
