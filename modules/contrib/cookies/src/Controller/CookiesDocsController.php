<?php

namespace Drupal\cookies\Controller;

use Drupal\cookies\Services\CookiesConfigService;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class ServiceConfigController.
 */
class CookiesDocsController extends ControllerBase {

  /**
   * Drupal\Core\Config\ConfigFactoryInterface definition.
   *
   * @var \Drupal\cookies\Services\CookiesConfigService
   */
  protected $configService;

  /**
   * Constructs a CookiesDocsController object.
   *
   * @param \Drupal\cookies\Services\CookiesConfigService $config_service
   *   The config factory.
   */
  public function __construct(CookiesConfigService $config_service) {
    $this->configService = $config_service;
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
   * Main method returning the documentation.
   *
   * @return array
   *   Returns render array with all the cookie docs collected from services.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function info() {
    return $this->configService->getRenderedCookiesDocs();
  }

}
