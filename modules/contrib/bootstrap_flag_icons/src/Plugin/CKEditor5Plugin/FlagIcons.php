<?php

namespace Drupal\bootstrap_flag_icons\Plugin\CKEditor5Plugin;

use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableInterface;
use Drupal\ckeditor5\Plugin\CKEditor5PluginConfigurableTrait;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefault;
use Drupal\ckeditor5\Plugin\CKEditor5PluginDefinition;
use Drupal\Component\Serialization\Json;
use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Extension\ModuleExtensionList;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\editor\EditorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * CKEditor 5 Bootstrap Icon plugin.
 */
class FlagIcons extends CKEditor5PluginDefault implements CKEditor5PluginConfigurableInterface, ContainerFactoryPluginInterface {

  use CKEditor5PluginConfigurableTrait;

  /**
   * Constructs a new AssetResolver instance.
   *
   * {@inheritDoc}
   */
  public function __construct(array $configuration, string $plugin_id, CKEditor5PluginDefinition $plugin_definition, protected ModuleExtensionList $moduleExtensionList, protected LibraryDiscoveryInterface $libraryDiscovery) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('extension.list.module'),
      $container->get('library.discovery'),
    );
  }

  /**
   * {@inheritDoc}
   */
  public function defaultConfiguration() {
    return [
      'cdn_flag' => FALSE,
      'img' => FALSE,
      'ratio' => '4x3',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['cdn_flag'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Icon flag CDN'),
      '#description' => $this->t("Enable if your admin theme does not support."),
      '#default_value' => $this->configuration['cdn_flag'] ?? FALSE,
    ];
    $form['img'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show image'),
      '#description' => $this->t("It will add image."),
      '#default_value' => $this->configuration['img'] ?? FALSE,
    ];
    $form['ratio'] = [
      '#type' => 'select',
      '#title' => $this->t('Ratio'),
      '#options' => ['1x1' => '1x1', '4x3' => '4:3'],
      '#description' => $this->t("1:1 or 4:3."),
      '#default_value' => $this->configuration['ratio'] ?? '4x3',
    ];
    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    return FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['cdn_flag'] = (boolean) $form_state->getValue('cdn_flag') ?? FALSE;
    $this->configuration['img'] = (boolean) $form_state->getValue('img') ?? FALSE;
    $this->configuration['ratio'] = (string) $form_state->getValue('ratio') ?? '4x3';
  }

  /**
   * {@inheritdoc}
   *
   * Get search list flag icon in editor config.
   */
  public function getDynamicPluginConfig(array $static_plugin_config, EditorInterface $editor): array {

    $file_path = dirname(__FILE__) . '/../../../js/iconSearch.json';
    $data = file_get_contents($file_path);
    $searchList = Json::decode($data);
    $ratio = $this->configuration['ratio'];
    $cdn = FALSE;
    global $base_path;
    $url = $base_path . $this->moduleExtensionList->getPath('bootstrap_flag_icons');
    if (!empty($this->configuration['cdn_flag'])) {
      $library_info = $this->libraryDiscovery->getLibraryByName('bootstrap_flag_icons', 'flag-icons');
      $cdn = $library_info["css"][0]["data"];
      $tmp = explode('/css/', $cdn);
      $url = $tmp[0];
    }
    $url .= '/flags/' . $ratio . '/';
    $config = $this->configuration;
    $config['url'] = $url;
    $config['search_list'] = $searchList;
    $config['cdn'] = $cdn;
    return [
      'flag_icons' => $config,
    ];
  }

}
