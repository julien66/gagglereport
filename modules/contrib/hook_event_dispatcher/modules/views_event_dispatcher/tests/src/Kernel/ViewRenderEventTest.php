<?php

namespace Drupal\Tests\views_event_dispatcher\Kernel;

use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\views_event_dispatcher\Event\Views\ViewsPostRenderEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsPreRenderEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewEventTest.
 *
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsPostRenderEvent
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsPreRenderEvent
 *
 * @group hook_event_dispatcher
 * @group views_event_dispatcher
 */
class ViewRenderEventTest extends ViewsEventKernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'user',
    'node',
    'views',
    'hook_event_dispatcher',
    'views_event_dispatcher',
  ];

  /**
   * Render events.
   *
   * @throws \Exception
   */
  public function testRenderEvent(): void {
    $this->listen(ViewsHookEvents::VIEWS_PRE_RENDER, 'onPreRender');
    $this->listen(ViewsHookEvents::VIEWS_POST_RENDER, 'onPostRender');

    $this->installEntitySchema('node');
    $render = $this->views->render();

    $this->assertEquals('Test', $this->views->getTitle());
    $this->assertContains('test', $render['#attached']['library']);
  }

  /**
   * Callback for ViewsPreRenderEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPreRenderEvent $event
   *   The event.
   */
  public function onPreRender(ViewsPreRenderEvent $event): void {
    $event->getView()->setTitle('Test');
  }

  /**
   * Callback for ViewsPostRenderEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPostRenderEvent $event
   *   The event.
   */
  public function onPostRender(ViewsPostRenderEvent $event): void {
    $this->assertEquals($this->view->id(), $event->getCache()->view->id());

    $output = &$event->getOutput();
    $output['#attached']['library'][] = 'test';
  }

}
