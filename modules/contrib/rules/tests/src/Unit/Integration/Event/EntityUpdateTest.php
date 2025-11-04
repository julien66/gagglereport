<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\Event;

/**
 * Checks that the entity update events are defined.
 *
 * @coversDefaultClass \Drupal\rules\Plugin\RulesEvent\EntityUpdateDeriver
 *
 * @group RulesEvent
 */
class EntityUpdateTest extends EventTestBase {

  /**
   * Tests the event metadata.
   */
  public function testEventMetadata(): void {
    $plugin_definition = $this->eventManager->getDefinition('rules_entity_update:test');
    $this->assertSame('After updating a test entity', (string) $plugin_definition['label']);
    $context_definition = $plugin_definition['context_definitions']['test'];
    $this->assertSame('entity:test', $context_definition->getDataType());
    $this->assertSame('Test', $context_definition->getLabel());

    // Also check that there is a context for the original entity.
    $context_definition = $plugin_definition['context_definitions']['test_unchanged'];
    $this->assertSame('entity:test', $context_definition->getDataType());
    $this->assertSame('Unchanged test entity', (string) $context_definition->getLabel());
  }

}
