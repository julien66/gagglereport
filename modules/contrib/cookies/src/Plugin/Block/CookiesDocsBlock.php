<?php

namespace Drupal\cookies\Plugin\Block;

use Drupal\cookies\Services\CookiesConfigService;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Cookies Documentation' block.
 *
 * @Block(
 *  id = "cookies_docs_block",
 *  admin_label = @Translation("Cookies Documentation"),
 * )
 */
class CookiesDocsBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The COOKiES config service.
   *
   * @var \Drupal\cookies\Services\CookiesConfigService
   */
  protected $configService;

  /**
   * Instance of Drupal\Core\Config\ConfigFactoryInterface.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructor for COOKiES Documentation block.
   *
   * @param array $configuration
   *   Block config.
   * @param string $plugin_id
   *   The plugin ID.
   * @param mixed $plugin_definition
   *   Block plugun definition.
   * @param \Drupal\cookies\Services\CookiesConfigService $cookies_config_service
   *   The config serve providing the drupalSettings (JS).
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   Configuration factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CookiesConfigService $cookies_config_service, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->configService = $cookies_config_service;
    $this->configFactory = $config_factory;
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
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): CookiesDocsBlock {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('cookies.config'),
      $container->get('config.factory')
    );
  }

  /**
   * Builds the settings buttons above and below.
   */
  public function buildSettingsButtons() {
    $open_settings_hash = $this->configFactory
      ->get('cookies.config')
      ->get('open_settings_hash');

    return [
      '#type' => 'link',
      '#title' => $this->t('Cookie settings'),
      '#url' => Url::fromUri("internal:#" . htmlspecialchars($open_settings_hash, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), [
        'attributes' => [
          'class' => ['button', 'cookies-open-cookie-consent-dialog'],
          'title' => $this->t('Edit cookie settings'),
        ],
      ]),
    ];
  }

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function build(): array {
    $renderArray = [];
    $showCookiesSettingsButton = $this->configuration['show_cookies_settings_button'];
    // Add cookies settings button above, if setting set:
    if (is_array($showCookiesSettingsButton) && in_array('above', $showCookiesSettingsButton)) {
      $renderArray['settings_button_above'] = $this->buildSettingsButtons();
    }

    // Add cookies docs:
    $renderArray['cookies_docs'] = $this->configService->getRenderedCookiesDocs();

    // Add cookies settings button below, if setting set:
    if (is_array($showCookiesSettingsButton) && in_array('below', $showCookiesSettingsButton)) {
      $renderArray['settings_button_below'] = $this->buildSettingsButtons();
    }

    return $renderArray;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    $configuration = parent::defaultConfiguration();
    $configuration['show_cookies_settings_button'] = [];
    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['show_cookies_settings_button'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Show COOKiES settings button'),
      '#description' => $this->t('Optionally show COOKiES settings button to open the cookie preferences dialog. Note: Requires Cookies UI block to be present on the page to work!'),
      '#default_value' => $this->configuration['show_cookies_settings_button'],
      '#options' => [
        'above' => $this->t('Above'),
        'below' => $this->t('Below'),
      ],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['show_cookies_settings_button'] = $form_state->getValue('show_cookies_settings_button');
    parent::submitConfigurationForm($form, $form_state);
  }

}
