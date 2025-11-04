<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\File;

use Drupal\core_event_dispatcher\Event\File\FileTransferInfoAlterEvent;
use Drupal\core_event_dispatcher\Event\File\FileTransferInfoEvent;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class FileTransferInfoEvent.
 *
 * @covers \Drupal\core_event_dispatcher\Event\File\FileTransferInfoAlterEvent
 * @covers \Drupal\core_event_dispatcher\Event\File\FileTransferInfoEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class FileTransferInfoEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test FileTransferInfoEvent.
   *
   * @throws \Exception
   */
  public function testFileTransferInfoEvent(): void {
    $this->listen(FileHookEvents::FILE_TRANSFER_INFO, 'onFileTransferInfo');
    $this->listen(FileHookEvents::FILE_TRANSFER_INFO_ALTER, 'onFileTransferInfoAlter');

    $info = drupal_get_filetransfer_info();
    $this->assertArrayHasKey('test', $info);
    $this->assertEquals([
      'title' => 'Test',
      'class' => self::class,
      'weight' => 10,
    ], $info['test']);
  }

  /**
   * Callback for FileTransferInfoEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\File\FileTransferInfoEvent $event
   *   The event.
   */
  public function onFileTransferInfo(FileTransferInfoEvent $event): void {
    $this->assertFalse($event->hasDefinition('test'));
    $event->addDefinition('test', 'Test', self::class, 10);
  }

  /**
   * Callback for FileTransferInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\File\FileTransferInfoAlterEvent $event
   *   The event.
   */
  public function onFileTransferInfoAlter(FileTransferInfoAlterEvent $event): void {
    $this->assertTrue($event->hasDefinition('system_test'));
    $definition = $event->getDefinition('system_test');
    $this->assertNotEquals(0, $definition['weight']);
    $definition['weight'] = 0;
    $event->setDefinition('system_test', $definition);
  }

}
