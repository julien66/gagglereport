<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\ConfigImportStepsAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\ConfigTestTrait;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class ConfigImportStepsAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\ConfigImportStepsAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ConfigImportStepsAlterEventTest extends KernelTestBase {

  use ConfigTestTrait;
  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'config_test',
    'system',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the ConfigImportStepsAlterEvent.
   *
   * @throws \Exception
   */
  public function testConfigImportStepsAlterEvent(): void {
    $this->installConfig(['system']);
    $this->copyConfig($this->container->get('config.storage'), $this->container->get('config.storage.sync'));

    $this->listen(CoreHookEvents::CONFIG_IMPORT_STEPS_ALTER, 'onConfigImportStepsAlter');

    $context = [];

    $configImporter = $this->configImporter();
    $syncSteps = $configImporter->initialize();
    $this->assertEquals([self::class, 'syncStep'], $syncSteps[2]);
    $configImporter->doSyncStep($syncSteps[2], $context);

    $this->assertNotEmpty($this->configImporter()->getErrors());
    $this->assertNotEmpty($context);
  }

  /**
   * Callback for ConfigImportStepsAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\ConfigImportStepsAlterEvent $event
   *   The event.
   */
  public function onConfigImportStepsAlter(ConfigImportStepsAlterEvent $event): void {
    $syncSteps = &$event->getSyncSteps();
    $syncSteps[] = [self::class, 'syncStep'];

    $configImporter = $event->getConfigImporter();
    $this->assertEmpty($configImporter->getErrors());
    $configImporter->logError('test');
  }

  /**
   * Implements configuration synchronization step added by an alter event.
   *
   * @param array $context
   *   The batch context.
   */
  public static function syncStep(array &$context): void {
    $context[] = TRUE;
  }

}
