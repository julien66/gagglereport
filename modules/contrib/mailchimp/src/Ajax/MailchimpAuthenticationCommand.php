<?php

namespace Drupal\mailchimp\Ajax;

use Drupal\Core\Ajax\CommandInterface;

/**
 * Authentication AJAX command.
 */
class MailchimpAuthenticationCommand implements CommandInterface {

  /**
   * URL to use for authentication.
   *
   * @var string
   */
  protected $url;

  /**
   * Temporary token issued from Mailchimp server.
   *
   * @var string
   */
  protected $tempToken;

  /**
   * The domain being authenticated.
   *
   * @var string
   */
  protected $domain;

  /**
   * Authtentication command constructor.
   *
   * @param string $url
   *   URL to use for authentication.
   * @param string $tempToken
   *   Temporary token to use in authentication process.
   * @param string $domain
   *   User ID to use in authentication process.
   */
  public function __construct($url, $tempToken, $domain) {
    $this->url = $url;
    $this->tempToken = $tempToken;
    $this->domain = $domain;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'authentication',
      'url' => $this->url,
      'temp_token' => $this->tempToken,
      'domain' => $this->domain,
    ];
  }

}
