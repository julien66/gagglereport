<?php

declare(strict_types=1);

namespace Drupal\Tests\hook_event_dispatcher\Unit;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;
use Drupal\hook_event_dispatcher\HookEventDispatcherModuleHandler;
use Drupal\hook_event_dispatcher\HookEventPluginManagerInterface;
use Drupal\hook_event_dispatcher\Manager\HookEventDispatcherManager;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\hook_event_dispatcher\HookEventDispatcherModuleHandler
 * @covers ::__construct()
 */
class HookEventDispatcherModuleHandlerTest extends TestCase {

  /**
   * @covers ::invokeAll
   * @covers ::invokeAllWith
   *
   * @dataProvider invokeProvider
   */
  public function testInvoke(bool|array $arg): void {
    $inner = $this->createMock(ModuleHandlerInterface::class);
    $inner->expects($this->once())->method('invokeAllWith');

    $dispatcherManager = $this->createMock(HookEventDispatcherManager::class);
    $dispatcherManager->expects($this->once())->method('register');

    $pluginManager = $this->createMock(HookEventPluginManagerInterface::class);
    $pluginManager->method('getHookEventFactories')->willReturnCallback(function (string $hook) {
      $this->assertEquals('hook', $hook);
      yield static function ($arg) {
        return new class($arg) implements EventInterface, HookReturnInterface {

          public function __construct(protected bool|array $arg) {}

          public function getDispatcherType(): string {
            return '';
          }

          public function getReturnValue() {
            return $this->arg;
          }

        };
      };
    });

    $moduleHandler = new HookEventDispatcherModuleHandler($inner, $dispatcherManager, $pluginManager);
    $this->assertEquals(is_array($arg) ? $arg : [$arg], $moduleHandler->invokeAll('hook', [$arg]));
  }

  public static function invokeProvider(): \Generator {
    yield [FALSE];
    yield [TRUE];
    yield [[FALSE, TRUE]];
    yield [[TRUE, FALSE]];
    yield [[FALSE, FALSE]];
    yield [[TRUE, TRUE]];
  }

  /**
   * @covers ::alter
   *
   * @dataProvider alterProvider
   */
  public function testAlter(bool ...$args): void {
    $inner = $this->createMock(ModuleHandlerInterface::class);
    $inner->expects($this->once())->method('alter');

    $dispatcherManager = $this->createMock(HookEventDispatcherManager::class);
    $dispatcherManager->expects($this->once())->method('register');

    $pluginManager = $this->createMock(HookEventPluginManagerInterface::class);
    $pluginManager->method('getAlterEventFactories')->willReturnCallback(function ($type) {
      $this->assertEquals('alter', $type);
      yield static function (&$data, &$context1 = NULL, &$context2 = NULL) {
        return new class($data, $context1, $context2) implements EventInterface {

          public function __construct(mixed &$data, mixed &$context1, mixed &$context2) {
            $data = !$data;
            $context1 = !$context1;
            $context2 = !$context2;
          }

          public function getDispatcherType(): string {
            return '';
          }

        };
      };
    });

    [$data, $context1, $context2] = $args;

    $moduleHandler = new HookEventDispatcherModuleHandler($inner, $dispatcherManager, $pluginManager);
    $moduleHandler->alter('alter', $data, $context1, $context2);
    $this->assertEquals($args, [!$data, !$context1, !$context2]);
  }

  public static function alterProvider(): \Generator {
    yield [TRUE, TRUE, TRUE];
    yield [TRUE, TRUE, FALSE];
    yield [TRUE, FALSE, FALSE];
    yield [TRUE, FALSE, TRUE];
    yield [FALSE, FALSE, FALSE];
    yield [FALSE, FALSE, TRUE];
    yield [FALSE, TRUE, TRUE];
    yield [FALSE, TRUE, FALSE];
  }

}
