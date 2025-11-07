<?php

namespace Drupal\file_example;

use Drupal\stream_wrapper_example\SessionHelper;

/**
 * A wrapper of session helper for file_example.
 */
class FileExampleSessionHelperWrapper {

  /**
   * Constructs a new FileExampleSessionHelperWrapper object.
   *
   * @param \Drupal\stream_wrapper_example\SessionHelper $sessionHelper
   *   The session helper.
   *
   * @see https://php.watch/versions/8.0/constructor-property-promotion
   */
  public function __construct(protected SessionHelper $sessionHelper) {
  }

  /**
   * Resets our stored data.
   */
  public function clearStoredData() {
    $this->sessionHelper->cleanUpStore();
  }

  /**
   * Gets our stored data for display.
   */
  public function getStoredData() {
    return $this->sessionHelper->getPath('');
  }

}
