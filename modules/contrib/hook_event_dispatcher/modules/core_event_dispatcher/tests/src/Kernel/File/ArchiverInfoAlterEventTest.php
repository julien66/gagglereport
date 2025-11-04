<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\File;

use Drupal\core_event_dispatcher\Event\File\ArchiverInfoAlterEvent;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class ArchiverInfoAlterEvent.
 *
 * @covers \Drupal\core_event_dispatcher\Event\File\ArchiverInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ArchiverInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test ArchiverInfoAlterEvent.
   *
   * @throws \Exception
   */
  public function testArchiverInfoAlterEvent(): void {
    $this->listen(FileHookEvents::ARCHIVER_INFO_ALTER, 'onArchiverInfoAlter');

    $archiverManager = $this->container->get('plugin.manager.archiver');
    $this->assertFalse($archiverManager->hasDefinition('Zip'));

    $definition = $archiverManager->getDefinition('Tar');
    $this->assertContains('tar.bzip', $definition['extensions']);
  }

  /**
   * Callback for ArchiverInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\File\ArchiverInfoAlterEvent $event
   *   The event.
   */
  public function onArchiverInfoAlter(ArchiverInfoAlterEvent $event): void {
    $this->assertTrue($event->hasDefinition('Zip'));
    $event->deleteDefinition('Zip');

    $definition = $event->getDefinition('Tar');
    $this->assertNotContains('tar.bzip', $definition['extensions']);
    $definition['extensions'][] = 'tar.bzip';
    $event->setDefinition('Tar', $definition);
  }

}
