<?php

namespace Drupal\Tests\examples\Unit;

/**
 * Trait for tests that retrieve files in project directories.
 */
trait FilesTestTrait {

  /**
   * Gets the absolute pathname for a project directory.
   *
   * @param string $directory
   *   The directory to check. If no directory name is passed, it will return
   *   the absolute path for the directory containing the Examples project.
   *
   * @return string
   *   The absolute pathname.
   */
  protected static function projectPathname(string $directory = ''): string {
    // There is no need to use realpath() because __DIR__ is always an absolute
    // path.
    return dirname(__DIR__, 3) . "/$directory";
  }

  /**
   * Retrieves files in a project directory.
   *
   * @param string $directory
   *   The directory where files are located. It must be an absolute path like
   *   the one returned by
   *   \Drupal\Tests\UnitTestCase\FilesTestTrait::realPath().
   * @param string $extension
   *   The file extension.
   * @param callable $callable
   *   The callable to accept files. It gets the following arguments.
   *     - $current: the current item's value
   *     - $key: the current item's key
   *     - $iterator: the iterator being filtered
   *   It must return TRUE when the file is accepted, FALSE otherwise.
   *
   * @return array
   *   An array
   *
   * @see \RecursiveCallbackFilterIterator
   */
  protected static function retrieveFiles(
    string $directory,
    string $extension,
    callable $callable,
  ): array {
    $directory_iterator = new \RecursiveDirectoryIterator(
      $directory,
      \FilesystemIterator::SKIP_DOTS
    );
    $files = [];
    $filter_callback = function (mixed $current, string $key, mixed $iterator) use ($extension, $callable) {
      /** @var \SplFileInfo $current */
      /** @var \RecursiveDirectoryIterator $iterator */
      if ($current->isFile() && $current->getExtension() === $extension) {
        return call_user_func($callable, $current, $key, $iterator);
      }
      elseif ($current->isDir()) {
        // Always accept a directory.
        return TRUE;
      }
      else {
        return FALSE;
      }
    };
    $filter = new \RecursiveCallbackFilterIterator($directory_iterator, $filter_callback);
    $iterator = new \RecursiveIteratorIterator($filter);

    foreach ($iterator as $info) {
      /** @var \SplFileInfo $info */
      $files[] = [$info->getFilename(), $info->getPath(), $info->getPathname()];
    }

    return $files;
  }

}
