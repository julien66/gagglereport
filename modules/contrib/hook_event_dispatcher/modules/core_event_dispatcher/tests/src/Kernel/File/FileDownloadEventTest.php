<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\File;

use Drupal\core_event_dispatcher\Event\File\FileDownloadEvent;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\system\FileDownloadController;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * Class FileDownloadEvent.
 *
 * @covers \Drupal\core_event_dispatcher\Event\File\FileDownloadEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class FileDownloadEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'file_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * The file download controller.
   *
   * @var \Drupal\system\FileDownloadController
   */
  protected FileDownloadController $controller;

  /**
   * The generated file name.
   *
   * @var string
   */
  protected string $filename;

  /**
   * {@inheritdoc}
   *
   * @throws \Exception
   */
  protected function setUp(): void {
    parent::setUp();
    $this->controller = new FileDownloadController($this->container->get('stream_wrapper_manager'));
  }

  /**
   * Test FileDownloadEvent without subscribers.
   *
   * @throws \Exception
   */
  public function testFileDownloadEventEmpty(): void {
    $this->expectException(AccessDeniedHttpException::class);
    $this->controller->download(Request::create('/dummy/example.txt', 'GET', ['file' => $this->generateTestFile()]), 'dummy-readonly');
  }

  /**
   * Generate a test file.
   *
   * @return string
   *   The filename of the test file.
   */
  protected function generateTestFile(): string {
    $filename = $this->randomMachineName();
    $sitePath = $this->container->getParameter('site.path');
    $filepath = $sitePath . '/files/' . $filename;
    file_put_contents($filepath, $filename);
    return $filename;
  }

  /**
   * Test FileDownloadEvent with forbidden access.
   *
   * @throws \Exception
   */
  public function testFileDownloadEventForbidden(): void {
    $this->listen(FileHookEvents::FILE_DOWNLOAD, 'onFileDownloadForbidden');
    $this->expectException(AccessDeniedHttpException::class);

    $this->filename = $this->generateTestFile();
    $this->controller->download(Request::create('/dummy/example.txt', 'GET', ['file' => $this->filename]), 'dummy-readonly');
  }

  /**
   * Callback for FileDownloadEventForbidden.
   *
   * @param \Drupal\core_event_dispatcher\Event\File\FileDownloadEvent $event
   *   The event.
   */
  public function onFileDownloadForbidden(FileDownloadEvent $event): void {
    $event->setForbidden();
  }

  /**
   * Test FileDownloadEvent.
   *
   * @throws \Exception
   */
  public function testFileDownloadEvent(): void {
    $this->listen(FileHookEvents::FILE_DOWNLOAD, 'onFileDownload');

    $this->filename = $this->generateTestFile();
    $response = $this->controller->download(Request::create('/dummy/example.txt', 'GET', ['file' => $this->filename]), 'dummy-readonly');

    $this->assertTrue($response->headers->has('x-foo'));
    $this->assertEquals('Bar', $response->headers->get('x-foo'));
    $this->assertEquals($this->filename, $response->getFile()->getFilename());
  }

  /**
   * Callback for FileDownloadEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\File\FileDownloadEvent $event
   *   The event.
   */
  public function onFileDownload(FileDownloadEvent $event): void {
    $this->assertEquals('dummy-readonly://' . $this->filename, $event->getUri());
    $event->setHeader('x-foo', 'Bar');
  }

}
