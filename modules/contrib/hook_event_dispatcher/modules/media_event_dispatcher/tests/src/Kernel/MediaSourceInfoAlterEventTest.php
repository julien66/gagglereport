<?php

namespace Drupal\Tests\media_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\media_event_dispatcher\Event\Media\MediaSourceInfoAlterEvent;
use Drupal\media_event_dispatcher\MediaHookEvents;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class MediaSourceInfoAlterEventTest.
 *
 * @covers \Drupal\media_event_dispatcher\Event\Media\MediaSourceInfoAlterEvent
 *
 * @group hook_event_dispatcher
 * @group media_event_dispatcher
 */
class MediaSourceInfoAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'hook_event_dispatcher',
    'media_event_dispatcher',
  ];

  /**
   * MediaSourceInfoAlterEvent sources array alter test.
   *
   * This tests altering the media source plugin definitions array.
   *
   * @throws \Exception
   */
  public function testMediaSourceInfoAlter(): void {
    $this->listen(MediaHookEvents::MEDIA_SOURCE_INFO_ALTER, 'onMediaSourceInfoAlter');

    $definitions = $this->container->get('plugin.manager.media.source')->getDefinitions();
    $this->assertArrayHasKey('test_source', $definitions);
    $this->assertEquals('test', $definitions['test_source']);
  }

  /**
   * Callback for MediaSourceInfoAlterEvent.
   *
   * @param \Drupal\media_event_dispatcher\Event\Media\MediaSourceInfoAlterEvent $event
   *   The event.
   */
  public function onMediaSourceInfoAlter(MediaSourceInfoAlterEvent $event): void {
    $sources = &$event->getSources();
    $sources['test_source'] = 'test';
  }

}
