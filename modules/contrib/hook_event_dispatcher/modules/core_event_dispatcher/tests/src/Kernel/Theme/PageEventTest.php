<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\Core\Render\HtmlResponse;
use Drupal\core_event_dispatcher\Event\Theme\PageAttachmentsEvent;
use Drupal\core_event_dispatcher\Event\Theme\PageBottomEvent;
use Drupal\core_event_dispatcher\Event\Theme\PageTopEvent;
use Drupal\core_event_dispatcher\PageHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\PageAttachmentsEvent
 * @covers \Drupal\core_event_dispatcher\Event\Theme\PageBottomEvent
 * @covers \Drupal\core_event_dispatcher\Event\Theme\PageTopEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class PageEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test the PageAttachmentsEvent, PageTopEvent, and PageBottomEvent.
   *
   * @throws \Exception
   */
  public function testPageEvents(): void {
    $this->listen(PageHookEvents::PAGE_ATTACHMENTS, 'onPageAttachments');
    $this->listen(PageHookEvents::PAGE_TOP, 'onPageTop');
    $this->listen(PageHookEvents::PAGE_BOTTOM, 'onPageBottom');

    $response = $this->container->get('main_content_renderer.html')->renderResponse(
      [],
      $this->container->get('request_stack')->getCurrentRequest(),
      $this->container->get('current_route_match'),
    );
    $this->assertInstanceOf(HtmlResponse::class, $response);

    $attachments = $response->getAttachments();
    $this->assertArrayHasKey('library', $attachments);
    $this->assertContains('test/test', $attachments['library']);

    $this->assertStringContainsString('Top!', $response->getContent());
    $this->assertStringContainsString('Bottom!', $response->getContent());
  }

  /**
   * Callback for PageAttachmentsEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\PageAttachmentsEvent $event
   *   The event.
   */
  public function onPageAttachments(PageAttachmentsEvent $event): void {
    $attachments = &$event->getAttachments();
    $attachments['#attached']['library'][] = 'test/test';
  }

  /**
   * Callback for PageTopEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\PageTopEvent $event
   *   The event.
   */
  public function onPageTop(PageTopEvent $event): void {
    $build = &$event->getBuild();
    $build['new'] = ['#markup' => 'Top!'];
  }

  /**
   * Callback for PageBottomEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\PageBottomEvent $event
   *   The event.
   */
  public function onPageBottom(PageBottomEvent $event): void {
    $build = &$event->getBuild();
    $build['new'] = ['#markup' => 'Bottom!'];
  }

}
