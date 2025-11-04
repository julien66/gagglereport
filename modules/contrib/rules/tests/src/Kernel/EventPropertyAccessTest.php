<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Kernel;

use Drupal\rules\Context\ContextConfig;
use Drupal\rules_test_event\Event\PlainEvent;
use Drupal\rules_test_event\Event\GenericEvent;
use Drupal\rules_test_event\Event\GetterEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Tests that Rules can use and access the properties of any Events.
 *
 * @group RulesEvent
 */
class EventPropertyAccessTest extends RulesKernelTestBase {

  /**
   * The entity storage for Rules config entities.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rules_test_event',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storage = $this->container->get('entity_type.manager')->getStorage('rules_reaction_rule');
  }

  /**
   * Tests that all event properties may be accessed.
   *
   * Properties declared in the MODULENAME.rules.events.yml metadata are
   * accessed by the \Drupal\rules\EventSubscriber\GenericEventSubscriber. If
   * these properties are not present or not visible to the event subscriber,
   * executing a Rule that uses these properties will throw an exception.
   *
   * @param string $event_name
   *   The Plugin ID of the event being tested.
   * @param \Symfony\Contracts\EventDispatcher\Event $event
   *   The event object being tested.
   *
   * @dataProvider provideTestEvent
   */
  public function testEventProperties($event_name, Event $event): void {
    $rule = $this->expressionManager->createRule();
    $rule->addCondition('rules_test_true');
    $rule->addAction('rules_test_debug_log',
      ContextConfig::create()
        ->map('message', 'publicProperty')
    );
    $rule->addAction('rules_test_debug_log',
      ContextConfig::create()
        ->map('message', 'protectedProperty')
    );
    $rule->addAction('rules_test_debug_log',
      ContextConfig::create()
        ->map('message', 'privateProperty')
    );

    $config_entity = $this->storage->create([
      'id' => 'test_event_rule',
      'events' => [['event_name' => $event_name]],
      'expression' => $rule->getConfiguration(),
    ]);
    $config_entity->save();

    // The logger instance has changed, refresh it.
    $this->logger = $this->container->get('logger.channel.rules_debug');
    $this->logger->addLogger($this->debugLog);

    $dispatcher = $this->container->get('event_dispatcher');

    // Remove all the listeners except Rules before triggering an event.
    $listeners = $dispatcher->getListeners(KernelEvents::REQUEST);
    foreach ($listeners as $listener) {
      if (empty($listener[1]) || $listener[1] != 'onRulesEvent') {
        $dispatcher->removeListener(KernelEvents::REQUEST, $listener);
      }
    }
    // Manually trigger the initialization event.
    $dispatcher->dispatch($event, $event_name);

    // Test that the action in the rule logged something.
    $this->assertRulesDebugLogEntryExists('public property', 0);
    $this->assertRulesDebugLogEntryExists('protected property', 1);
    $this->assertRulesDebugLogEntryExists('private property', 2);
  }

  /**
   * Data provider for events to test.
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
