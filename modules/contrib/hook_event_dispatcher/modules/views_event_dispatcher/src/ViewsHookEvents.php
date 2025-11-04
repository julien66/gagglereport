<?php

namespace Drupal\views_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for views hooks.
 */
final class ViewsHookEvents {

  /**
   * Describe data tables and fields (or the equivalent) to Views.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsDataEvent
   * @see views_event_dispatcher_views_data()
   * @see hook_views_data()
   *
   * @var string
   */
  public const VIEWS_DATA = HookEventDispatcherInterface::PREFIX . 'views.data';

  /**
   * Alter the table and field information from hook_views_data().
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsDataAlterEvent
   * @see views_event_dispatcher_views_data_alter()
   * @see hook_views_data_alter()
   *
   * @var string
   */
  public const VIEWS_DATA_ALTER = HookEventDispatcherInterface::PREFIX . 'views.data_alter';

  /**
   * Replace special strings in the query before it is executed.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsQuerySubstitutionsEvent
   * @see views_event_dispatcher_views_query_substitutions()
   * @see hook_views_query_substitutions()
   *
   * @var string
   */
  public const VIEWS_QUERY_SUBSTITUTIONS = HookEventDispatcherInterface::PREFIX . 'views.query_substitutions';

  /**
   * Alter a view at the very beginning of Views processing.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsPreViewEvent
   * @see views_event_dispatcher_views_pre_view()
   * @see hook_views_pre_view()
   *
   * @var string
   */
  public const VIEWS_PRE_VIEW = HookEventDispatcherInterface::PREFIX . 'views.pre_view';

  /**
   * Act on the view before the query is built, but after displays are attached.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsPreBuildEvent
   * @see views_event_dispatcher_views_pre_build()
   * @see hook_views_pre_build()
   *
   * @var string
   */
  public const VIEWS_PRE_BUILD = HookEventDispatcherInterface::PREFIX . 'views.pre_build';

  /**
   * Act on the view immediately after the query is built.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsPostBuildEvent
   * @see views_event_dispatcher_views_post_build()
   * @see hook_views_post_build()
   *
   * @var string
   */
  public const VIEWS_POST_BUILD = HookEventDispatcherInterface::PREFIX . 'views.post_build';

  /**
   * Act on the view after the query is built and just before it is executed.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsPreExecuteEvent
   * @see views_event_dispatcher_views_pre_execute()
   * @see hook_views_pre_execute()
   *
   * @var string
   */
  public const VIEWS_PRE_EXECUTE = HookEventDispatcherInterface::PREFIX . 'views.pre_execute';

  /**
   * Act on the view immediately after the query has been executed.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsPostExecuteEvent
   * @see views_event_dispatcher_views_post_execute()
   * @see hook_views_post_execute()
   *
   * @var string
   */
  public const VIEWS_POST_EXECUTE = HookEventDispatcherInterface::PREFIX . 'views.post_execute';

  /**
   * Act on the view immediately before rendering it.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsPreRenderEvent
   * @see views_event_dispatcher_views_pre_render()
   * @see hook_views_pre_render()
   *
   * @var string
   */
  public const VIEWS_PRE_RENDER = HookEventDispatcherInterface::PREFIX . 'views.pre_render';

  /**
   * Post-process any rendered data.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsPostRenderEvent
   * @see views_event_dispatcher_views_post_render()
   * @see hook_views_post_render()
   *
   * @var string
   */
  public const VIEWS_POST_RENDER = HookEventDispatcherInterface::PREFIX . 'views.post_render';

  /**
   * Alter the query before it is executed.
   *
   * @Event
   *
   * @see \Drupal\views_event_dispatcher\Event\Views\ViewsQueryAlterEvent
   * @see views_event_dispatcher_views_query_alter()
   * @see hook_views_query_alter()
   *
   * @var string
   */
  public const VIEWS_QUERY_ALTER = HookEventDispatcherInterface::PREFIX . 'views.query_alter';

}
