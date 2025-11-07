<?php

namespace Drupal\Tests\examples\Unit;

/**
 * Trait for tests that needs to retrieve configuration files.
 */
trait RetrieveConfigFilesTrait {

  /**
   * Callback to filter Drupal configuration files.
   *
   * @param mixed $current
   *   The element being filtered.
   * @param string $key
   *   The element key.
   * @param mixed $iterator
   *   The iterator.
   *
   * @return bool
   *   Whether the current file is a Drupal configuration file.
   */
  protected static function configFilesCallback(mixed $current, string $key, mixed $iterator): bool {
    // A Drupal configuration file is either contained in a config/install or a
    // config/optional directory. The config/schema directory contains the
    // schemas for the configuration files.
    /** @var \SplFileInfo $current */
    /** @var \RecursiveDirectoryIterator $iterator */
    $path = $current->getPath();
    return str_ends_with($path, '/config/install') ||
      str_ends_with($path, '/config/optional');
  }

}
