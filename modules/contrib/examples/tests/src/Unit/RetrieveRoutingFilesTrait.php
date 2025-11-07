<?php

namespace Drupal\Tests\examples\Unit;

/**
 * Trait for test that needs to retrieve routing files.
 */
trait RetrieveRoutingFilesTrait {

  /**
   * Callback to filter Drupal routing files.
   *
   * @param mixed $current
   *   The element being filtered.
   * @param string $key
   *   The element key.
   * @param mixed $iterator
   *   The iterator.
   *
   * @return bool
   *   Whether the current file is a Drupal config file.
   */
  protected static function routingFilesCallback(mixed $current, string $key, mixed $iterator): bool {
    // A Drupal routing file has a filename that ends with .routing.yml, and it
    // is contained in a directory containing a .info.yml file with the same
    // name.
    // For example, node.routing.yml is a routing file if in the same directory
    // there is also a node.info.yml file.
    /** @var \SplFileInfo $current */
    /** @var \RecursiveDirectoryIterator $iterator */
    if (str_ends_with($current->getFileName(), '.routing.yml')) {
      return file_exists(substr($current->getPathName(), 0, -12) . '.info.yml');
    }

    return FALSE;
  }

}
