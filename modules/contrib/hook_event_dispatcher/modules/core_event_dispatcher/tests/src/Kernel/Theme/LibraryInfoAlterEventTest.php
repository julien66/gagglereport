<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\core_event_dispatcher\Event\Theme\LibraryInfoAlterEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\LibraryInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class LibraryInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the LibraryInfoAlterEventTest.
   *
   * @throws \Exception
   */
  public function testLibraryInfoAlterEvent(): void {
    $this->listen(ThemeHookEvents::LIBRARY_INFO_ALTER, 'onLibraryInfoAlter');

    $libraries = $this->container->get('library.discovery.parser')->buildByExtension('core');
    $this->assertArrayHasKey('test_library', $libraries);
  }

  /**
   * Callback for LibraryInfoAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\LibraryInfoAlterEvent $event
   *   The event.
   */
  public function onLibraryInfoAlter(LibraryInfoAlterEvent $event): void {
    $this->assertEquals('core', $event->getExtension());
    $libraries = &$event->getLibraries();
    $libraries['test_library'] = [
      'drupalSettings' => [],
    ];
  }

}
