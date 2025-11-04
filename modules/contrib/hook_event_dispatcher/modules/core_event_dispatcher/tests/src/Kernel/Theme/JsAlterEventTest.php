<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\Core\Asset\AttachedAssets;
use Drupal\core_event_dispatcher\Event\Theme\JsAlterEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\JsAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class JsAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'common_test',
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * JsAlterEvent test.
   *
   * @throws \Exception
   */
  public function testJsAlterEvent(): void {
    $this->listen(ThemeHookEvents::JS_ALTER, 'onJsAlter');

    $build = [
      '#attached' => [
        'library' => ['common_test/files'],
      ],
    ];
    $assets = AttachedAssets::createFromRenderArray($build);

    $assets = $this->container->get('asset.resolver')->getJsAssets($assets, FALSE);
    $this->assertArrayHasKey('other', $assets[0]);
    $this->assertEquals([
      'group' => 0,
      'scope' => 'header',
    ], $assets[0]['other']);
  }

  /**
   * Callback for JsAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\JsAlterEvent $event
   *   The event.
   */
  public function onJsAlter(JsAlterEvent $event): void {
    $this->assertContains('common_test/files', $event->getAttachedAssets()->getLibraries());
    $javascript = &$event->getJavascript();
    $javascript['other'] = [
      'group' => 0,
      'scope' => 'header',
    ];
  }

}
