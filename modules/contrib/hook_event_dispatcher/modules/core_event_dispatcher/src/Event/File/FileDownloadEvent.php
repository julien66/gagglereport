<?php

namespace Drupal\core_event_dispatcher\Event\File;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class FileDownloadEvent.
 */
#[HookEvent(id: 'file_download', hook: 'file_download')]
class FileDownloadEvent extends Event implements EventInterface, HookReturnInterface {

  /**
   * Forbids the download if set to TRUE.
   *
   * @var bool
   */
  protected bool $forbidden = FALSE;

  /**
   * Response headers that will be set for the downloaded file.
   *
   * @var array
   */
  protected array $headers = [];

  /**
   * FileDownloadEvent constructor.
   *
   * @param string $uri
   *   The URI of the file.
   */
  public function __construct(protected readonly string $uri) {
  }

  /**
   * Checks if the download is forbidden.
   *
   * @return bool
   *   TRUE if the download is forbidden.
   */
  public function isForbidden(): bool {
    return $this->forbidden;
  }

  /**
   * Sets the download as forbidden.
   */
  public function setForbidden(): void {
    $this->forbidden = TRUE;
  }

  /**
   * Gets the response headers.
   *
   * @return array
   *   The response headers.
   */
  public function getHeaders(): ?array {
    return $this->headers;
  }

  /**
   * Sets the header.
   *
   * @param string $name
   *   The header name.
   * @param mixed $value
   *   The header value.
   */
  public function setHeader(string $name, $value): void {
    $this->headers[$name] = $value;
  }

  /**
   * Gets the URI from the file.
   *
   * @return string
   *   The URI of the file.
   */
  public function getUri(): string {
    return $this->uri;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FileHookEvents::FILE_DOWNLOAD;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->isForbidden() ? -1 : $this->getHeaders();
  }

}
