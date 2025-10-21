<?php

namespace Drupal\Tests\cookies_asset_injector\Traits;

use Drupal\asset_injector\Entity\AssetInjectorJs;
use Drupal\Core\Cache\Cache;

/**
 * Provides methods to create a media type from given values.
 *
 * This trait is meant to be used only by test classes.
 */
trait CookiesAssetInjectorTestHelperTrait {

  /**
   * Helper function to create an asset injector config entity.
   */
  protected function createAssetInjector(string $id, string $label, string $code, bool $header = FALSE, bool $preprocess = FALSE, string|NULL $cookiesService = NULL) {
    $thirdPartySettingsArray = [];
    if (!empty($cookiesService)) {
      $thirdPartySettingsArray =
      [
        'cookies_asset_injector' => [
          'cookies_service' => $cookiesService,
        ],
      ];
    }
    $assetInjectorInstance = AssetInjectorJs::create([
      'id' => $id,
      'label' => $label,
      'code' => $code,
      'conditions_require_all' => TRUE,
      'conditions' => [],
      'contexts' => [],
      'header' => $header,
      'preprocess' => $preprocess,
      'third_party_settings' => $thirdPartySettingsArray,
      'jquery' => FALSE,
      'noscript' => '',
      'noscriptRegion' => [],
    ],
    'asset_injector_js');
    $assetInjectorInstance->save();

    Cache::invalidateTags(['config:asset_injector.js']);

    return $assetInjectorInstance->get('id');
  }

}
