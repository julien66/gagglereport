<?php

namespace Drupal\Tests\hook_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Service provider test.
 *
 * @group hook_event_dispatcher
 */
class HookEventDispatcherServiceProviderTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'tracer',
  ];

  /**
   * Tests that the tracer plugin manager use decorated module handler instead.
   */
  public function testTracerPluginManagerArgument(): void {
    $definition = $this->container->getDefinition('plugin.manager.tracer');
    $argument = $definition->getArgument(2);
    $this->assertInstanceOf(Reference::class, $argument);
    $this->assertEquals('hook_event_dispatcher.module_handler.inner', $argument);
  }

}
