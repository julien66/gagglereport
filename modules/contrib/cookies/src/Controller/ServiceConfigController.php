<?php

namespace Drupal\cookies\Controller;

use Drupal\cookies\Services\CookiesConfigService;
use Drupal\Core\Cache\CacheableJsonResponse;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Delivers config for COOKiES JS-frontend.
 */
class ServiceConfigController extends ControllerBase {

  /**
   * The famous Drupal Language Manager.
   *
   * @var \Drupal\cookies\Services\CookiesConfigService
   */
  protected $cookiesConfigService;

  /**
   * ServiceConfigController constructor.
   *
   * @param \Drupal\cookies\Services\CookiesConfigService $cookies_config_service
   *   The CookiesConfigService from this project.
   */
  public function __construct(CookiesConfigService $cookies_config_service) {
    $this->cookiesConfigService = $cookies_config_service;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('cookies.config')
    );
  }

  /**
   * Get service configuration.
   *
   * @param string $lang
   *   The translation lang.
   *
   * @return \Drupal\Core\Cache\CacheableJsonResponse
   *   Return as a JSON response.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function getJson($lang = 'en') {
    $data = $this->cookiesConfigService->getCookiesConfig($lang);

    $data['#cache'] = [
      'contexts' => ['url', 'languages'],
      'tags' => [
        'config:cookies.config',
        'config:cookies.texts',
        'config:cookies.cookies_service',
        'config:cookies.cookies_service_group',
      ],
    ];

    $response = new CacheableJsonResponse($data);
    $response->addCacheableDependency(CacheableMetadata::createFromRenderArray($data));
    return $response;
  }

}
