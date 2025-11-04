<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\Event;

use Drupal\rules\Core\RulesEventManager;
use Drupal\rules_test_event\Event\PlainEvent;
use Drupal\rules_test_event\Event\GenericEvent;
use Drupal\rules_test_event\Event\GetterEvent;
use Symfony\Component\EventDispatcher\GenericEvent as SymfonyGenericEvent;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Checks that the events defined in the rules_test_event module are correct.
 *
 * @group RulesEvent
 */
class EventPropertyAccessTest extends EventTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // Must enable our test module to make its plugins discoverable.
    $this->enableModule('rules_test_event', [
      'Drupal\\rules_test_event' => __DIR__ . '/../../../../modules/rules_test_event/src',
    ]);

    // Tell the plugin manager where to look for plugins.
    $this->moduleHandler->getModuleDirectories()
      ->willReturn(['rules_test_event' => __DIR__ . '/../../../../modules/rules_test_event/']);

    // Create a real plugin manager with a mock moduleHandler.
    $this->eventManager = new RulesEventManager($this->moduleHandler->reveal(), $this->entityTypeBundleInfo->reveal());
  }

  /**
   * Tests the event metadata to ensure that all properties may be accessed.
   *
   * Access to properties declared in the metadata is tested using code copied
   * from \Drupal\rules\EventSubscriber\GenericEventSubscriber, so this test
   * does not directly test GenericEventSubscriber - that is done correctly
   * in the Kernel test \Drupal\Tests\rules\Kernel\EventPropertyAccessTest.
   *
   * @param string $event_name
   *   The Plugin ID of the event being tested.
   * @param \Symfony\Contracts\EventDispatcher\Event $event
   *   The event object being tested.
   *
   * @dataProvider provideTestEvent
   */
  public function testEventContextDefinition($event_name, Event $event): void {
    $plugin = $this->eventManager->createInstance($event_name);
    $context_definitions = $plugin->getContextDefinitions();
    foreach ($context_definitions as $name => $definition) {
      $this->assertSame('string', $definition->getDataType());
      // Properties for these test events should be named <visibility>Property.
      // We just want the <visibility> part.
      $visibility = substr($name, 0, -8);
      $this->assertSame('A ' . $visibility . ' string', $definition->getLabel());

      // If this is a GenericEvent, get the value of the context variable from
      // the event arguments.
      if ($event instanceof SymfonyGenericEvent) {
        $value = $event->getArgument($name);
      }
      // If there is a getter method set in the event definition, use that.
      elseif ($definition->hasGetter()) {
        $value = $event->{$definition->getGetter()}();
      }
      // Else we cheat and use a closure to get the property value.
      else {
        $getter = function ($property) {
          return $this->{$property};
        };
        $value = $getter->call($event, $name);
      }
      $this->assertEquals($visibility . ' property', $value);
    }
  }

  /**
   * Data provider - provides events to test.
   *
   * Passes an event name and an event object for each test case.
   * Here we use all the events defined in the rules_test_event test module.
   *
   * @return array
   *   Array of array of values to be passed to our test.
   */
  public static function provideTestEvent(): array {
    return [
      'Plain event' => [
        'rules_test_event.plain_event',
        new PlainEvent(),
      ],
      'Generic event' => [
        'rules_test_event.generic_event',
        new GenericEvent('Test subject', [
          'publicProperty' => 'public property',
          'protectedProperty' => 'protected property',
          'privateProperty' => 'private property',
        ]),
      ],
      'Getter event' => [
        'rules_test_event.getter_event',
        new GetterEvent(),
      ],
    ];
  }

}
