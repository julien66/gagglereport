<?php

namespace Drupal\file_example\Traits;

/**
 * Provides a trait for dumper service.
 */
trait DumperTrait {

  /**
   * Gets the dumper service.
   *
   * @return \Drupal\devel\DevelDumperManagerInterface|bool
   *   The dumper service or FALSE if the service is not available.
   */
  protected function dumper() {
    if (\Drupal::hasService('devel.dumper')) {
      /** @var \Drupal\devel\DevelDumperManagerInterface $dumper */
      $dumper = \Drupal::service('devel.dumper');
      return $dumper;
    }
    return FALSE;
  }

}
