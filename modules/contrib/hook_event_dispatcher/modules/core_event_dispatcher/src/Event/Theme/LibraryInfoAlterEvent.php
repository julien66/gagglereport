<?php

namespace Drupal\core_event_dispatcher\Event\Theme;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class LibraryInfoAlterEvent.
 */
#[HookEvent(id: 'library_info_alter', alter: 'library_info')]
class LibraryInfoAlterEvent extends Event implements EventInterface {

  /**
   * Libraries.
   *
   * @var array
   */
  private array $libraries = [];

  /**
   * LibraryInfoAlterEvent constructor.
   *
   * @param array $libraries
   *   An associative array of libraries registered by $extension.
   *   Keyed by internal library.
   * @param string $extension
   *   Can either be 'core' or the machine name of the extension
   *   that registered the libraries.
   */
  public function __construct(array &$libraries, private readonly string $extension) {
    $this->libraries = &$libraries;
  }

  /**
   * Get libraries info.
   *
   * @return array
   *   Libraries info.
   */
  public function &getLibraries(): array {
    return $this->libraries;
  }

  /**
   * Get the extension.
   *
   * @return string
   *   The extension.
   */
  public function getExtension(): string {
    return $this->extension;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return ThemeHookEvents::LIBRARY_INFO_ALTER;
  }

}
