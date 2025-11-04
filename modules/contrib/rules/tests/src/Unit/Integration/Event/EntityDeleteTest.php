<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\Event;

/**
 * Checks that the entity delete events are defined.
 *
 * @coversDefaultClass \Drupal\rules\Plugin\RulesEvent\EntityDeleteDeriver
 *
 * @group RulesEvent
 */
class EntityDeleteTest extends EventTestBase {

  /**
   * Tests the event metadata.
   */
  public function testEventMetadata(): void {
    $plugin_definition = $this->eventManager->getDefinition('rules_entity_delete:test');
    $this->assertSame('After deleting a test entity', (string) $plugin_definition['label']);
    $context_definition = $plugin_definition['context_definitions']['test'];
    $this->assertSame('entity:test', $context_definition->getDataType());
    $this->assertSame('Test', $context_definition->getLabel());
  }

}
