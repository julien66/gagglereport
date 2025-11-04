<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Unit\Integration\Event;

/**
 * Checks that the entity view events are defined.
 *
 * @coversDefaultClass \Drupal\rules\Plugin\RulesEvent\EntityViewDeriver
 *
 * @group RulesEvent
 */
class EntityViewTest extends EventTestBase {

  /**
   * Tests the event metadata.
   */
  public function testEventMetadata(): void {
    $plugin_definition = $this->eventManager->getDefinition('rules_entity_view:test');
    $this->assertSame('Entity of type test is viewed', (string) $plugin_definition['label']);
    $context_definition = $plugin_definition['context_definitions']['test'];
    $this->assertSame('entity:test', $context_definition->getDataType());
    $this->assertSame('Test', $context_definition->getLabel());
  }

}
