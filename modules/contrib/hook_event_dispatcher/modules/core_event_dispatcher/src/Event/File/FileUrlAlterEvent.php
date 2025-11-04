<?php

namespace Drupal\core_event_dispatcher\Event\File;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class FileUrlAlterEvent.
 */
#[HookEvent(id: 'file_url_alter', alter: 'file_url')]
class FileUrlAlterEvent extends Event implements EventInterface {

  /**
   * The URI to a file for which we need an external URL.
   *
   * @var string
   */
  protected $uri;

  /**
   * FileUrlAlterEvent constructor.
   */
  public function __construct(string &$uri) {
    $this->uri = &$uri;
  }

  /**
   * Gets the URI to a file for which we need an external URL.
   *
   * @return string
   *   The URI to a file for which we need an external URL.
   */
  public function getUri(): string {
    return $this->uri;
  }

  /**
   * Sets the URI to a file for which we need an external URL.
   *
   * @param string $uri
   *   The URI to a file for which we need an external URL.
   */
  public function setUri(string $uri): void {
    $this->uri = $uri;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FileHookEvents::FILE_URL_ALTER;
  }

}
