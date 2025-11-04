<?php

namespace Drupal\Tests\views_event_dispatcher\Kernel;

use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\views_event_dispatcher\Event\Views\AbstractViewsEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewEventTest.
 *
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsPostExecuteEvent
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsPreExecuteEvent
 *
 * @group hook_event_dispatcher
 * @group views_event_dispatcher
 */
class ViewExecuteEventTest extends ViewsEventKernelTestBase {

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
   * Execute events.
   *
   * @throws \Exception
   */
  public function testExecuteEvent(): void {
    $this->listen([
      ViewsHookEvents::VIEWS_PRE_EXECUTE,
      ViewsHookEvents::VIEWS_POST_EXECUTE,
    ], 'onExecute', $this->exactly(2));

    $this->installEntitySchema('node');
    $this->views->execute();

    $this->assertEquals('Test', $this->views->getTitle());
  }

  /**
   * Callback for ViewsPreExecuteEvent and ViewsPostExecuteEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\AbstractViewsEvent $event
   *   The event.
   */
  public function onExecute(AbstractViewsEvent $event): void {
    $event->getView()->setTitle('Test');
  }

}
