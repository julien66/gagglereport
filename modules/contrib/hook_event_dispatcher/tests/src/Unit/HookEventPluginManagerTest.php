<?php

namespace Drupal\Tests\hook_event_dispatcher\Unit;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\HookEventPluginManager;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\hook_event_dispatcher\HookEventPluginManager
 * @covers ::__construct()
 * @covers ::<!public>
 */
class HookEventPluginManagerTest extends TestCase {

  /**
   * @covers ::getHookEventFactories
   * @covers ::getAlterEventFactories
   *
   * @dataProvider eventFactoriesProvider
   */
  public function testHookEventFactories(string $type): void {
    $event = new class() extends Event implements EventInterface {

      public function getDispatcherType(): string {
        return '';
      }

    };
    $cacheBackend = $this->createMock(CacheBackendInterface::class);
    $cacheBackend->method('get')->willReturn((object) [
      'data' => [
        'test' => [
          'class' => $event::class,
          'id' => 'test',
          $type => 'test',
        ],
      ],
    ]);

    $manager = new HookEventPluginManager(new \ArrayObject(), $cacheBackend, []);
    $factories = match ($type) {
      'hook' => $manager->getHookEventFactories('test'),
      'alter' => $manager->getAlterEventFactories('test'),
      default => throw new \UnhandledMatchError(),
    };
    $this->assertTrue($factories->valid());

    $factory = $factories->current();
    $this->assertIsCallable($factory);
    $this->assertInstanceOf($event::class, $factory());
  }

  public static function eventFactoriesProvider(): \Generator {
    yield ['hook'];
    yield ['alter'];
  }

}
