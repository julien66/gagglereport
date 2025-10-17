<?php

namespace Drupal\social_auth_facebook\Settings;

use Drupal\social_auth\Settings\SettingsInterface;

/**
 * Defines the settings interface.
 */
interface FacebookAuthSettingsInterface extends SettingsInterface {

  /**
   * Gets the graph version.
   *
   * @return string|null
   *   The version.
   */
  public function getGraphVersion(): ?string;

}
