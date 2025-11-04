<?php

namespace Drupal\core_event_dispatcher;

use Drupal\hook_event_dispatcher\HookEventDispatcherInterface;

/**
 * Defines events for page hooks.
 */
final class PageHookEvents {

  /**
   * Add a renderable array to the top of the page.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\PageTopEvent
   * @see core_event_dispatcher_page_top()
   * @see hook_page_top()
   *
   * @var string
   */
  public const PAGE_TOP = HookEventDispatcherInterface::PREFIX . 'page.top';

  /**
   * Add a renderable array to the bottom of the page.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\PageBottomEvent
   * @see core_event_dispatcher_page_bottom()
   * @see hook_page_bottom()
   *
   * @var string
   */
  public const PAGE_BOTTOM = HookEventDispatcherInterface::PREFIX . 'page.bottom';

  /**
   * Add attachments (typically assets) to a page before it is rendered.
   *
   * Attachments should be added to individual element render arrays whenever
   * possible, as per Drupal best practices, so only use this when that isn't
   * practical or you need to target the page itself.
   *
   * @Event
   *
   * @see \Drupal\core_event_dispatcher\Event\Theme\PageAttachmentsEvent
   * @see core_event_dispatcher_page_attachments()
   * @see hook_page_attachments()
   *
   * @var string
   */
  public const PAGE_ATTACHMENTS = HookEventDispatcherInterface::PREFIX . 'page.attachments';

}
