<?php

namespace Drupal\social_auth_facebook\Plugin\Network;

use Drupal\social_api\SocialApiException;
use Drupal\social_auth\Plugin\Network\NetworkBase;
use Drupal\social_auth\Plugin\Network\NetworkInterface;
use Drupal\social_auth\Settings\SettingsInterface;
use League\OAuth2\Client\Provider\Facebook;

/**
 * Defines a Network Plugin for Social Auth Facebook.
 *
 * @package Drupal\social_auth_facebook\Plugin\Network
 *
 * @Network(
 *   id = "social_auth_facebook",
 *   short_name = "facebook",
 *   social_network = "Facebook",
 *   img_path = "img/facebook_logo.svg",
 *   type = "social_auth",
 *   class_name = "\League\OAuth2\Client\Provider\Facebook",
 *   auth_manager = "\Drupal\social_auth_facebook\FacebookAuthManager",
 *   routes = {
 *     "redirect": "social_auth.network.redirect",
 *     "callback": "social_auth.network.callback",
 *     "settings_form": "social_auth.network.settings_form",
 *   },
 *   handlers = {
 *     "settings": {
 *       "class": "\Drupal\social_auth_facebook\Settings\FacebookAuthSettings",
 *       "config_id": "social_auth_facebook.settings"
 *     }
 *   }
 * )
 */
class FacebookAuth extends NetworkBase implements NetworkInterface {

  /**
   * {@inheritdoc}
   */
  protected function initSdk(): mixed {

    $class_name = '\League\OAuth2\Client\Provider\Facebook';
    if (!class_exists($class_name)) {
      throw new SocialApiException(sprintf('The PHP League OAuth2 library for Facebook not found. Class: %s.', $class_name));
    }

    /** @var \Drupal\social_auth_facebook\Settings\FacebookAuthSettings $settings */
    $settings = $this->settings;
    if ($this->validateConfig($settings)) {
      // All these settings are mandatory.
      $league_settings = [
        'clientId'          => $settings->getClientId(),
        'clientSecret'      => $settings->getClientSecret(),
        'redirectUri'       => $this->getCallbackUrl()->setAbsolute()->toString(),
        'graphApiVersion'   => 'v' . $settings->getGraphVersion(),
      ];

      // Proxy configuration data for outward proxy.
      $config = $this->siteSettings->get('http_client_config');
      if (!empty($config['proxy']['http'])) {
        $league_settings['proxy'] = $config['proxy']['http'];
      }
      return new Facebook($league_settings);
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function validateConfig(SettingsInterface $settings): bool {
    $graph_version = $settings->getGraphVersion();
    if (!parent::validateConfig($settings) || !$graph_version) {
      $this->loggerFactory
        ->get('social_auth_facebook')
        ->error('Define graph version on module settings.');
      return FALSE;
    }
    return TRUE;
  }

}
