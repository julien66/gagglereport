<?php

namespace Drupal\Tests\hook_event_dispatcher\Unit\Event;

use Drupal\Component\Plugin\Definition\PluginDefinition;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Drupal\hook_event_dispatcher\Event\PluginDefinitionAlterEventBase
 * @covers ::__construct()
 */
class PluginDefinitionAlterEventBaseTest extends TestCase {

  /**
   * @covers ::getDefinitions
   */
  public function testGetDefinitions(): void {
    $definitions = [
      'test_plugin' => $this->createMock(PluginDefinition::class),
    ];
    $event = self::getEventClass($definitions);
    $this->assertEquals($definitions, $event->getDefinitions());
  }

  /**
   * @covers ::setDefinition
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function testSetDefinition(): void {
    $definitions = [];
    $even = self::getEventClass($definitions);
    $even->setDefinition('test_plugin', ['test' => TRUE]);
    $this->assertEquals(['test' => TRUE], $even->getDefinition('test_plugin'));
  }

  /**
   * @covers ::deleteDefinition
   */
  public function testDeleteDefinition(): void {
    $this->expectException(PluginNotFoundException::class);

    $definitions = [
      'test_plugin' => $this->createMock(PluginDefinition::class),
    ];
    $event = self::getEventClass($definitions);
    $event->deleteDefinition('test_plugin');
    $event->getDefinition('test_plugin');
  }

  private static function getEventClass(array &$definitions): PluginDefinitionAlterEventBase {
    return new class($definitions) extends PluginDefinitionAlterEventBase {

      public function getDispatcherType(): string {
        return '';
      }

    };
  }

}
