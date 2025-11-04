<?php

namespace Drupal\views_event_dispatcher\Event\Views;

use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ViewExecutable;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewsQueryAlterEvent.
 */
#[HookEvent(id: 'views_query_alter', hook: 'views_query_alter')]
final class ViewsQueryAlterEvent extends AbstractViewsEvent {

  /**
   * ViewsPreExecuteEvent constructor.
   *
   * @param \Drupal\views\ViewExecutable $view
   *   The view.
   * @param \Drupal\views\Plugin\views\query\QueryPluginBase $query
   *   The query.
   */
  public function __construct(ViewExecutable $view, private readonly QueryPluginBase $query) {
    parent::__construct($view);
  }

  /**
   * Get the query.
   *
   * @return \Drupal\views\Plugin\views\query\QueryPluginBase
   *   The query.
   */
  public function getQuery(): QueryPluginBase {
    return $this->query;
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return ViewsHookEvents::VIEWS_QUERY_ALTER;
  }

}
