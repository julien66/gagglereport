<?php

namespace Drupal\Tests\media_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\media\OEmbed\Provider;
use Drupal\media_event_dispatcher\Event\Media\OEmbedResourceUrlAlterEvent;
use Drupal\media_event_dispatcher\MediaHookEvents;
use Drupal\media_test_oembed\ProviderRepository;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class OEmbedResourceUrlAlterEventTest.
 *
 * @covers \Drupal\media_event_dispatcher\Event\Media\OEmbedResourceUrlAlterEvent
 *
 * @group hook_event_dispatcher
 * @group media_event_dispatcher
 */
class OEmbedResourceUrlAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'media',
    'media_test_oembed',
    'hook_event_dispatcher',
    'media_event_dispatcher',
  ];

  /**
   * Get a mock oEmbed provider.
   *
   * @return \Drupal\media\OEmbed\Provider
   *   A mock Vimeo oEmbed provider.
   *
   * @throws \Drupal\media\OEmbed\ProviderException
   */
  private function getMockProvider(): Provider {
    return new Provider('Vimeo', 'https://vimeo.com/', [
      [
        'url'     => 'https://vimeo.com/api/oembed.json',
        'schemes' => [
          'https://vimeo.com/*',
        ],
      ],
    ]);
  }

  /**
   * OEmbedResourceUrlAlterEvent parsed URL alter test.
   *
   * This tests altering the parsed URL array for an oEmbed data request.
   *
   * @throws \Exception
   */
  public function testOembedResourceParsedUrlAlter(): void {
    $this->listen(MediaHookEvents::MEDIA_OEMBED_RESOURCE_DATA_ALTER, 'onOembedResourceParsedUrlAlter');

    $providerRepository = $this->container->get('media.oembed.provider_repository');
    $this->assertInstanceOf(ProviderRepository::class, $providerRepository);
    $providerRepository->setProvider($this->getMockProvider());

    $url = $this->container->get('media.oembed.url_resolver')->getResourceUrl('https://vimeo.com/7073899');
    $this->assertEquals('https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/7073899&altered=1&width=1280', $url);
  }

  /**
   * Callback for OEmbedResourceUrlAlterEvent.
   *
   * @param \Drupal\media_event_dispatcher\Event\Media\OEmbedResourceUrlAlterEvent $event
   *   The event.
   */
  public function onOembedResourceParsedUrlAlter(OEmbedResourceUrlAlterEvent $event): void {
    $provider = $event->getProvider();
    $this->assertEquals('Vimeo', $provider->getName());

    $url = &$event->getParsedUrl();
    $url['query']['width'] = 1280;
  }

}
