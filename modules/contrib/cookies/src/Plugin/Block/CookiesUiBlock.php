<?php

namespace Drupal\cookies\Plugin\Block;

use Drupal\cookies\Services\CookiesConfigService;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'CookiesUiBlock' block.
 *
 * @Block(
 *  id = "cookies_ui_block",
 *  admin_label = @Translation("Cookies UI"),
 * )
 */
class CookiesUiBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * A config factory object.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * The COOKiES config service.
   *
   * @var \Drupal\cookies\Services\CookiesConfigService
   */
  protected $cookiesConfigService;

  /**
   * An entity type manager object.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The Cookies UI Block theme definition.
   */
  public const THEME_DEFINITION = 'cookies_block';

  /**
   * Constructor for COOKiES UI block.
   *
   * @param array $configuration
   *   Block config.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   Block plugun definition.
   * @param \Drupal\Core\Config\ConfigFactory $config_factory
   *   Config factory to return module config.
   * @param \Drupal\cookies\Services\CookiesConfigService $cookies_config_service
   *   The config serve providing the drupalSettings (JS).
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager object.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactory $config_factory, CookiesConfigService $cookies_config_service, EntityTypeManagerInterface $entityTypeManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configFactory = $config_factory;
    $this->cookiesConfigService = $cookies_config_service;
    $this->entityTypeManager = $entityTypeManager;
  }

  /**
   * Static creator for dependencies injection in blocks.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   Container delivers the services.
   * @param array $configuration
   *   Block config.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   Block plugun definition.
   *
   * @return static
   *   Static object instance.
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory'),
      $container->get('cookies.config'),
      $container->get('entity_type.manager'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $lib = $this->cookiesConfigService->getLibrary();
    $attached = [
      'library' => [$lib],
      'drupalSettings' => [
        'cookies' => $this->cookiesConfigService->getCookiesModuleData(),
      ],
    ];
    $build = [
      '#theme' => static::THEME_DEFINITION,
      '#styles' => !empty($this->configFactory->get('cookies.config')->get('use_default_styles')),
      '#attached' => $attached,
      '#cache' => [
        'contexts' => ['languages'],
        'tags' => [
          'config:cookies.config',
          'config:cookies.texts',
          'config:cookies.cookies_service',
          'config:cookies.cookies_service_group',
        ],
      ],
    ];
    return $build;
  }

}
