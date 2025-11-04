<?php

namespace Drupal\Tests\views_event_dispatcher\Kernel;

use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewEventTest.
 *
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent
 *
 * @group hook_event_dispatcher
 * @group views_event_dispatcher
 */
class ViewEventTest extends ViewsEventKernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'views',
    'hook_event_dispatcher',
    'views_event_dispatcher',
  ];

  /**
   * Pre view event.
   *
   * @throws \Exception
   */
  public function testPreViewEvent(): void {
    $this->listen(ViewsHookEvents::VIEWS_PRE_VIEW, 'onPreView');

    $this->views->setDisplay('default');
    $this->views->preExecute();

    $this->assertContains('test', $this->views->args);
  }

  /**
   * Callback for ViewsPreViewEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent $event
   *   The event.
   */
  public function onPreView(ViewsPreViewEvent $event): void {
    $arguments = &$event->getArguments();
    $arguments[] = 'test';

    $this->assertEquals($this->view->id(), $event->getView()->id());
    $this->assertEquals('default', $event->getDisplayId());
  }

}
