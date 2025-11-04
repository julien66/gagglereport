<?php

namespace Drupal\core_event_dispatcher\Event\File;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class FileMimetypeMappingAlterEvent.
 */
#[HookEvent(id: 'file_mimetype_mapping_alter', alter: 'file_mimetype_mapping')]
class FileMimetypeMappingAlterEvent extends Event implements EventInterface {

  /**
   * An array of mimetypes correlated to the extensions that relate to them.
   *
   * @var array
   */
  protected $mapping = [];

  /**
   * FileMimetypeMappingAlterEvent constructor.
   */
  public function __construct(array &$mapping) {
    $this->mapping = &$mapping;
  }

  /**
   * Sets mimetype mapping.
   *
   * @param int|string $key
   *   The key of the mimetype in the mapping array.
   * @param string $mimetype
   *   The mimetype to set.
   */
  public function setMimetypeMapping($key, string $mimetype): void {
    $this->mapping['mimetypes'][$key] = $mimetype;
  }

  /**
   * Sets extension mapping.
   *
   * @param string $extension
   *   The extension to set.
   * @param int|string $key
   *   The key of the mimetype in the mapping array.
   */
  public function setExtensionMapping(string $extension, $key): void {
    $this->mapping['extensions'][$extension] = $key;
  }

  /**
   * Unsets extension mapping.
   *
   * @param string $extension
   *   The extension to unset.
   */
  public function unsetExtensionMapping(string $extension): void {
    unset($this->mapping['extensions'][$extension]);
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FileHookEvents::FILE_MIMETYPE_MAPPING_ALTER;
  }

}
