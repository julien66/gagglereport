<?php

namespace Drupal\social_auth_facebook;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\Core\Utility\Error;
use Drupal\social_auth\AuthManager\OAuth2Manager;
use Drupal\social_auth\User\SocialAuthUser;
use Drupal\social_auth\User\SocialAuthUserInterface;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Contains all the logic for Facebook OAuth2 authentication.
 */
class FacebookAuthManager extends OAuth2Manager {

  /**
   * The Facebook client.
   *
   * @var \League\OAuth2\Client\Provider\Facebook
   */
  protected mixed $client;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Used for accessing configuration object factory.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $logger_factory
   *   The logger factory.
   * @param \Symfony\Component\HttpFoundation\RequestStack $request_stack
   *   Used to get the authorization code from the callback request.
   */
  public function __construct(
    ConfigFactory $configFactory,
    LoggerChannelFactoryInterface $logger_factory,
    RequestStack $request_stack,
  ) {
    parent::__construct(
      $configFactory->get('social_auth_facebook.settings'),
      $logger_factory,
      $this->request = $request_stack->getCurrentRequest());
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate(): void {
    try {
      $this->setAccessToken($this->client->getLongLivedAccessToken($this->client->getAccessToken('authorization_code',
        ['code' => $this->request->query->get('code')])));
    }
    catch (\Exception $e) {
      $this->loggerFactory->get('social_auth_facebook')
        ->error('There was an error during authentication. Exception: ' . $e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getUserInfo():? SocialAuthUserInterface {
    $access_token = $this->getAccessToken();
    if (!$this->user && $access_token !== NULL) {
      $owner = $this->client->getResourceOwner($this->getAccessToken());
      $this->user = new SocialAuthUser(
        $owner->getName(),
        $owner->getId(),
        $this->getAccessToken(),
        $owner->getEmail(),
        $owner->getPictureUrl(),
        $this->getExtraDetails()
      );
      $this->user->setFirstName($owner->getFirstName());
      $this->user->setLastName($owner->getLastName());
    }
    return $this->user;
  }

  /**
   * {@inheritdoc}
   */
  public function getAuthorizationUrl(): string {
    $scopes = ['email', 'public_profile'];

    $extra_scopes = $this->getScopes();
    if ($extra_scopes) {
      $scopes = array_merge($scopes, explode(',', $extra_scopes));
    }

    // Returns the URL where user will be redirected.
    return $this->client->getAuthorizationUrl([
      'scope' => $scopes,
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function requestEndPoint(string $method, string $path, ?string $domain = NULL, array $options = []): mixed {
    if (!$domain) {
      $domain = 'https://graph.facebook.com';
    }

    $url = $domain . '/v' . $this->settings->get('graph_version') . $path;
    $url .= '&access_token=' . $this->getAccessToken();
    try {
      $request = $this->client->getAuthenticatedRequest($method, $url, $this->getAccessToken(), $options);
    }
    catch (\Exception $e) {
      Error::logException($this->loggerFactory->get('social_auth_facebook'), $e);
      return NULL;
    }

    try {
      return $this->client->getParsedResponse($request);
    }
    catch (IdentityProviderException $e) {
      Error::logException($this->loggerFactory->get('social_auth_facebook'), $e);
    }

    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getState(): string {
    return $this->client->getState();
  }

}
