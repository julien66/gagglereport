<?php

namespace Drupal\Tests\cookies\Traits;

use Drupal\Core\Cache\Cache;

/**
 * A helper trait for the cookies module.
 */
trait CookiesCacheClearTrait {

  /**
   * Clears the backend caches and rebuilds the kernel container.
   */
  public function clearBackendCaches() {
    // This is part of drupal_flush_all_caches:
    // @todo Check why this is needed
    // https://www.drupal.org/project/cookies/issues/3326187
    foreach (Cache::getBins() as $cache_backend) {
      $cache_backend->deleteAll();
    }
    \Drupal::service('kernel')->rebuildContainer();
  }

}
