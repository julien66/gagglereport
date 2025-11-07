<?php

namespace Drupal\file_example;

use Drupal\Core\State\StateInterface;

/**
 * A state helper class for the file_example module.
 */
class FileExampleStateHelper {
  const KEY_DEFAULT_FILE = 'file_example_default_file';
  const KEY_DEFAULT_DIRECTORY = 'file_example_default_directory';
  const DEFAULT_FILE = 'session://drupal.txt';
  const DEFAULT_DIRECTORY = 'session://directory1';

  /**
   * Constructs a new FileExampleHelper object.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   The state.
   */
  public function __construct(protected StateInterface $state) {
  }

  /**
   * Gets the default file.
   *
   * @return string
   *   The URI of the default file.
   */
  public function getDefaultFile(): string {
    return $this->state->get(self::KEY_DEFAULT_FILE, self::DEFAULT_FILE);
  }

  /**
   * Sets the default file.
   *
   * @param string $uri
   *   URI to save for future display in the form.
   */
  public function setDefaultFile(string $uri): void {
    $this->state->set(self::KEY_DEFAULT_FILE, $uri);
  }

  /**
   * Gets the default directory.
   *
   * @return string
   *   The URI of the default directory.
   */
  public function getDefaultDirectory(): string {
    return $this->state->get(self::KEY_DEFAULT_DIRECTORY, self::DEFAULT_DIRECTORY);
  }

  /**
   * Sets the default directory.
   *
   * @param string $uri
   *   URI to save for later form display.
   */
  public function setDefaultDirectory(string $uri): void {
    $this->state->set(self::KEY_DEFAULT_DIRECTORY, $uri);
  }

  /**
   * Removes the default file and default directory from the state storage.
   */
  public function deleteDefaultState(): void {
    $this->state->delete(self::KEY_DEFAULT_DIRECTORY);
    $this->state->delete(self::KEY_DEFAULT_FILE);
  }

}
