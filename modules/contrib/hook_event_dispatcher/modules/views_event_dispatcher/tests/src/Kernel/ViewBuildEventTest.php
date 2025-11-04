<?php

namespace Drupal\Tests\views_event_dispatcher\Kernel;

use Drupal\Core\Database\Query\AlterableInterface;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\views_event_dispatcher\Event\Views\AbstractViewsEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsQueryAlterEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsQuerySubstitutionsEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewEventTest.
 *
 * @covers \Drupal\views_event_dispatcher\Event\Views\AbstractViewsEvent
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsPostBuildEvent
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsPreBuildEvent
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsQueryAlterEvent
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsQuerySubstitutionsEvent
 *
 * @group hook_event_dispatcher
 * @group views_event_dispatcher
 */
class ViewBuildEventTest extends ViewsEventKernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'views',
    'hook_event_dispatcher',
    'views_event_dispatcher',
  ];

  /**
   * Build events.
   *
   * @throws \Exception
   */
  public function testBuildEvent(): void {
    $this->listen([
      ViewsHookEvents::VIEWS_PRE_BUILD,
      ViewsHookEvents::VIEWS_POST_BUILD,
    ], 'onBuild', $this->exactly(2));
    $this->listen(ViewsHookEvents::VIEWS_QUERY_ALTER, 'onQueryAlter');
    $this->listen(ViewsHookEvents::VIEWS_QUERY_SUBSTITUTIONS, 'onQuerySubstitutions', $this->atLeastOnce());

    $this->views->build();

    $this->assertEquals(10, $this->views->getQuery()->getLimit());

    $this->assertArrayHasKey('query', $this->views->build_info);
    $query = $this->views->build_info['query'];
    $this->assertInstanceOf(AlterableInterface::class, $query);
    $metaData = $query->getMetaData('views_substitutions');
    $this->assertArrayHasKey('test', $metaData);
    $this->assertEquals('replacement', $metaData['test']);
  }

  /**
   * Callback for ViewsPreBuildEvent and ViewsPostBuildEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\AbstractViewsEvent $event
   *   The event.
   */
  public function onBuild(AbstractViewsEvent $event): void {
    $this->assertEquals($this->view->id(), $event->getView()->id());
  }

  /**
   * Callback for ViewsQueryAlterEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsQueryAlterEvent $event
   *   The event.
   */
  public function onQueryAlter(ViewsQueryAlterEvent $event): void {
    $event->getQuery()->setLimit(10);
  }

  /**
   * Callback for ViewsQuerySubstitutionsEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsQuerySubstitutionsEvent $event
   *   The event.
   */
  public function onQuerySubstitutions(ViewsQuerySubstitutionsEvent $event): void {
    $event->addSubstitution('test', 'replacement');
  }

}
