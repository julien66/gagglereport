<?php

namespace Drupal\core_event_dispatcher\Event\File;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Component\Plugin\Discovery\DiscoveryCachedTrait;
use Drupal\Component\Plugin\Discovery\DiscoveryInterface;
use Drupal\core_event_dispatcher\FileHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Drupal\hook_event_dispatcher\Event\HookReturnInterface;

/**
 * Class FileTransferInfoEvent.
 */
#[HookEvent(id: 'file_transfer_info', hook: 'filetransfer_info')]
class FileTransferInfoEvent extends Event implements EventInterface, DiscoveryInterface, HookReturnInterface {

  use DiscoveryCachedTrait;

  /**
   * FileTransferInfoEvent constructor.
   */
  public function __construct() {
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitions(): array {
    if (!$this->definitions) {
      $this->definitions = [];
    }

    return $this->definitions;
  }

  /**
   * Adds a file transfer definition.
   *
   * @param string $type
   *   FileTransfer type (not human readable, used for form elements and
   *   variable names, etc).
   * @param string|\Drupal\Core\StringTranslation\TranslatableMarkup $title
   *   The human-readable name of the connection type.
   * @param string $class
   *   The name of the FileTransfer class. The constructor
   *   will always be passed the full path to the root of the site that should
   *   be used to restrict where file transfer operations can occur (the $jail)
   *   and an array of settings values returned by the settings form.
   * @param int|null $weight
   *   Integer weight used for sorting connection types on the authorize.php
   *   form.
   */
  public function addDefinition(string $type, $title, string $class, ?int $weight = NULL): void {
    $this->definitions[$type] = [
      'title' => $title,
      'class' => $class,
    ];
    if ($weight !== NULL) {
      $this->definitions[$type]['weight'] = $weight;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return FileHookEvents::FILE_TRANSFER_INFO;
  }

  /**
   * {@inheritdoc}
   */
  public function getReturnValue() {
    return $this->getDefinitions();
  }

}
