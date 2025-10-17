<?php

namespace Drupal\social_auth_facebook\Settings;

use Drupal\social_auth\Settings\SettingsBase;

/**
 * Defines methods to get Social Auth Facebook app settings.
 */
class FacebookAuthSettings extends SettingsBase implements FacebookAuthSettingsInterface {

  /**
   * The default graph version.
   *
   * @var string|null
   */
  protected ?string $graphVersion;

  /**
   * {@inheritdoc}
   */
  public function getGraphVersion(): ?string {
    return $this->graphVersion = $this->config->get('graph_version');
  }

}
