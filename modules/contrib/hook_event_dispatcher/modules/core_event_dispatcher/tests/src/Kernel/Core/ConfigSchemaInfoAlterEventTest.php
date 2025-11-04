<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\config_translation\FormElement\Textarea;
use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\ConfigSchemaInfoAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class ConfigSchemaInfoAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\ConfigSchemaInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ConfigSchemaInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test ConfigSchemaInfoAlterEvent.
   *
   * @throws \Exception
   */
  public function testConfigSchemaInfoAlterEvent(): void {
    $this->listen(CoreHookEvents::CONFIG_SCHEMA_INFO_ALTER, 'onConfigSchemaInfoAlter');

    $typedConfigManager = $this->container->get('config.typed');
    $definition = $typedConfigManager->getDefinition('text');
    $this->assertArrayHasKey('form_element_class', $definition);
    $this->assertEquals(Textarea::class, $definition['form_element_class']);
  }

  /**
   * Callback for ConfigSchemaInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\ConfigSchemaInfoAlterEvent $event
   *   The event.
   */
  public function onConfigSchemaInfoAlter(ConfigSchemaInfoAlterEvent $event): void {
    $this->assertTrue($event->hasDefinition('text'));
    $definition = $event->getDefinition('text');
    $this->assertArrayNotHasKey('form_element_class', $definition);
    $definition['form_element_class'] = Textarea::class;
    $event->setDefinition('text', $definition);
  }

}
