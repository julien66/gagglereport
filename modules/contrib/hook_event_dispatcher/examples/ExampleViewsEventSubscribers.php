<?php

namespace Drupal\hook_event_dispatcher;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\views_event_dispatcher\Event\Views\ViewsPostBuildEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsPostExecuteEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsPostRenderEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsPreBuildEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsPreExecuteEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsPreRenderEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsQueryAlterEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsQuerySubstitutionsEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class ExampleViewsEventSubscribers.
 *
 * Don't forget to define your class as a service and tag it as
 * an "event_subscriber":
 *
 * services:
 *  hook_event_dispatcher.example_views_subscribers:
 *   class: Drupal\hook_event_dispatcher\ExampleViewsEventSubscribers
 *   tags:
 *     - { name: event_subscriber }
 */
class ExampleViewsEventSubscribers implements EventSubscriberInterface {

  /**
   * ExampleViewsEventSubscribers constructor.
   *
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(protected readonly TimeInterface $time) {
  }

  /**
   * Pre view event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent $event
   *   The event.
   */
  public function preView(ViewsPreViewEvent $event): void {
    $args = &$event->getArguments();

    // Do something with the arguments.
    $args[0] = 'custom value';
  }

  /**
   * Pre build event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPreBuildEvent $event
   *   The event.
   */
  public function preBuild(ViewsPreBuildEvent $event): void {
    $view = $event->getView();

    // Do something with the view.
    $view->setArguments(['test']);
  }

  /**
   * Query alter event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsQueryAlterEvent $event
   *   The event.
   */
  public function queryAlter(ViewsQueryAlterEvent $event): void {
    $query = $event->getQuery();

    // Do something with the query.
    $query->setLimit(10);
  }

  /**
   * Query substitutions event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsQuerySubstitutionsEvent $event
   *   The event.
   */
  public function querySubstitutions(ViewsQuerySubstitutionsEvent $event): void {
    $event->addSubstitution('***CURRENT_TIME***', (string) $this->time->getRequestTime());
  }

  /**
   * Post build event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPostBuildEvent $event
   *   The event.
   */
  public function postBuild(ViewsPostBuildEvent $event): void {
    $view = $event->getView();

    // Do something with the view.
    $view->setArguments(['test']);
  }

  /**
   * Pre execute event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPreExecuteEvent $event
   *   The event.
   */
  public function preExecute(ViewsPreExecuteEvent $event): void {
    $view = $event->getView();

    // Do something with the view.
    $view->setArguments(['test']);
  }

  /**
   * Post execute event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPostExecuteEvent $event
   *   The event.
   */
  public function postExecute(ViewsPostExecuteEvent $event): void {
    $view = $event->getView();

    // Do something with the view.
    $view->setArguments(['test']);
  }

  /**
   * Pre render event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPreRenderEvent $event
   *   The event.
   */
  public function preRender(ViewsPreRenderEvent $event): void {
    $view = $event->getView();

    // Do something with the view.
    $view->setArguments(['test']);
  }

  /**
   * Post render event handler.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsPostRenderEvent $event
   *   The event.
   */
  public function postRender(ViewsPostRenderEvent $event): void {
    $cache = $event->getCache();

    // Do something with the cache settings.
    $cache->options['results_lifespan'] = 0;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents(): array {
    return [
      ViewsHookEvents::VIEWS_PRE_VIEW => 'preView',
      ViewsHookEvents::VIEWS_PRE_BUILD => 'preBuild',
      ViewsHookEvents::VIEWS_QUERY_ALTER => 'queryAlter',
      ViewsHookEvents::VIEWS_QUERY_SUBSTITUTIONS => 'querySubstitutions',
      ViewsHookEvents::VIEWS_POST_BUILD => 'postBuild',
      ViewsHookEvents::VIEWS_PRE_EXECUTE => 'preExecute',
      ViewsHookEvents::VIEWS_POST_EXECUTE => 'postExecute',
      ViewsHookEvents::VIEWS_PRE_RENDER => 'preRender',
      ViewsHookEvents::VIEWS_POST_RENDER => 'postRender',
    ];
  }

}
