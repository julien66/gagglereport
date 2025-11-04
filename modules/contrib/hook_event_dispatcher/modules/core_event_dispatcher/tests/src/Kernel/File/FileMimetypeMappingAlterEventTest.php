<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\File;

use Drupal\core_event_dispatcher\Event\File\FileMimetypeMappingAlterEvent;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class FileMimetypeMappingAlterEvent.
 *
 * @covers \Drupal\core_event_dispatcher\Event\File\FileMimetypeMappingAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class FileMimetypeMappingAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test FileMimetypeMappingAlterEvent.
   *
   * @throws \Exception
   */
  public function testFileMimetypeMappingAlterEvent(): void {
    $this->listen(FileHookEvents::FILE_MIMETYPE_MAPPING_ALTER, 'onFileMimetypeMappingAlter');

    $testCase = [
      'foo.file_test_1' => 'bar/file_test_1',
      'foo.file_test_2' => 'bar/file_test_2',
      'foo.file_test_3' => 'bar/file_test_2',
      'foo.doc' => 'bar/doc',
      'test.ogg' => 'application/octet-stream',
    ];

    $guesser = $this->container->get('file.mime_type.guesser');
    // Test using default mappings.
    foreach ($testCase as $input => $expected) {
      $output = $guesser->guessMimeType($input);
      $this->assertEquals($expected, $output);
    }
  }

  /**
   * Callback for FileMimetypeMappingAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\File\FileMimetypeMappingAlterEvent $event
   *   The event.
   */
  public function onFileMimetypeMappingAlter(FileMimetypeMappingAlterEvent $event): void {
    // Add new mappings.
    $event->setMimetypeMapping('file_test_mimetype_1', 'bar/file_test_1');
    $event->setMimetypeMapping('file_test_mimetype_2', 'bar/file_test_2');
    $event->setMimetypeMapping('file_test_mimetype_3', 'bar/doc');
    $event->setExtensionMapping('file_test_1', 'file_test_mimetype_1');
    $event->setExtensionMapping('file_test_2', 'file_test_mimetype_2');
    $event->setExtensionMapping('file_test_3', 'file_test_mimetype_2');
    // Override existing mapping.
    $event->setExtensionMapping('doc', 'file_test_mimetype_3');

    $event->unsetExtensionMapping('ogg');
  }

}
