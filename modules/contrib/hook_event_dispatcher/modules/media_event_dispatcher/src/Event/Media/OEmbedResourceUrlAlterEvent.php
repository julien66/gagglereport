<?php

namespace Drupal\media_event_dispatcher\Event\Media;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\media\OEmbed\Provider;
use Drupal\media_event_dispatcher\MediaHookEvents;

/**
 * Class OEmbedResourceUrlAlterEvent.
 */
#[HookEvent(id: 'o_embed_resource_url_alter', alter: 'oembed_resource_url')]
class OEmbedResourceUrlAlterEvent extends Event implements EventInterface {

  /**
   * The oEmbed URL that data will be retrieved from.
   *
   * Note that this is an array as returned by
   * \Drupal\Component\Utility\UrlHelper::parse().
   *
   * @var array
   */
  private array $parsedUrl = [];

  /**
   * OEmbedResourceUrlAlterEvent constructor.
   *
   * @param array &$parsedUrl
   *   The oEmbed URL that data will be retrieved from, parsed into an array by
   *   \Drupal\Component\Utility\UrlHelper::parse().
   * @param \Drupal\media\OEmbed\Provider $provider
   *   The oEmbed provider for the resource to be retrieved.
   */
  public function __construct(array &$parsedUrl, private readonly Provider $provider) {
    $this->parsedUrl = &$parsedUrl;
  }

  /**
   * Get the URL that the oEmbed data will be retrieved from.
   *
   * Note that this is an array as returned by
   * \Drupal\Component\Utility\UrlHelper::parse().
   *
   * @return array
   *   The parsed URL for this oEmbed resource.
   */
  public function &getParsedUrl(): array {
    return $this->parsedUrl;
  }

  /**
   * Get the oEmbed provider for the resource to be retrieved.
   *
   * @return \Drupal\media\OEmbed\Provider
   *   The oEmbed provider for the resource.
   */
  public function getProvider(): Provider {
    return $this->provider;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return MediaHookEvents::MEDIA_OEMBED_RESOURCE_DATA_ALTER;
  }

}
