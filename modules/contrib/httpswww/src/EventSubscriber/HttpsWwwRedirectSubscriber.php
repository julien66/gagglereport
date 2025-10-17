<?php

namespace Drupal\httpswww\EventSubscriber;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * The Class HttpsWwwRedirectSubscriber.
 *
 * @package Drupal\httpswww\EventSubscriber
 */
class HttpsWwwRedirectSubscriber implements EventSubscriberInterface {

  /**
   * Module specific configuration.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * HttpsWwwRedirectSubscriber constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The current user.
   */
  public function __construct(ConfigFactoryInterface $config_factory, AccountInterface $account) {
    $this->config = $config_factory->get('httpswww.settings');
    $this->account = $account;
  }

  /**
   * Executes a redirect if one is needed based on config.
   *
   * @param \Symfony\Component\HttpKernel\Event\RequestEvent $event
   *   The event to process.
   */
  public function redirect(RequestEvent $event) {

    // Quit if it's not enabled.
    if (empty($this->config->get('enabled'))) {
      return;
    }

    // Quit if the user has the bypass permission.
    if ($this->account->hasPermission('bypass httpswww redirect')) {
      return;
    }

    $req_host = $event->getRequest()->getHost();
    $req_scheme = $event->getRequest()->getScheme();
    $req_url = $event->getRequest()->getSchemeAndHttpHost();

    $conf_scheme = $this->config->get('scheme') ?: 'mixed';
    $conf_prefix = $this->config->get('prefix') ?: 'mixed';
    $use_prefix = $conf_prefix === 'yes';

    // Set scheme.
    $new_scheme = $conf_scheme === 'mixed' ? $req_scheme : $conf_scheme;
    $new_host = $req_host;

    // Set/remove prefix.
    if ($conf_prefix !== 'mixed') {
      $domain_parts = explode('.', $req_host);
      $prefix = reset($domain_parts);
      $has_www = $prefix === 'www';
      $excl_subs = $this->config->get('exclude_subdomains') ?: [];

      if ($use_prefix && !$has_www && !in_array($prefix, $excl_subs)) {
        $new_host = 'www.' . $req_host;
      }
      elseif (!$use_prefix && $has_www) {
        $new_host = substr($req_host, 4);
      }
    }

    $new_url = $new_scheme . '://' . $new_host;

    // Check if the URL is valid and redirect if URLs doesn't match.
    if (UrlHelper::isValid($new_url, TRUE) && $req_url !== $new_url) {
      $new_url .= $event->getRequest()->getRequestUri();
      $response = new TrustedRedirectResponse($new_url, 301);

      $build = [
        '#cache' => [
          'max-age'  => 0,
          'contexts' => ['url', 'user.permissions'],
          'tags'     => ['config:httpswww.settings'],
        ],
      ];
      $cache_meta = CacheableMetadata::createFromRenderArray($build);
      $response->addCacheableDependency($cache_meta);
      $event->setResponse($response);
    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Set this to run as early as possible, but after the user authentication.
    $events[KernelEvents::REQUEST][] = ['redirect', 299];
    return $events;
  }

}
