<?php

namespace Drupal\exclude_node_title\Plugin\DsField\Node;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\ds\Plugin\DsField\Field;
use Drupal\ds\Plugin\DsField\Node\NodeTitle as DsNodeTitle;
use Drupal\ds\Plugin\DsField\Title;
use Drupal\exclude_node_title\ExcludeNodeTitleManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Extended NodeTitle Display Suite plugin.
 */
class NodeTitle extends DsNodeTitle {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, protected ExcludeNodeTitleManager $exclusionManager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('exclude_node_title.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm($form, FormStateInterface $form_state): array {
    $settings = Title::settingsForm($form, $form_state);

    $config = $this->getConfiguration();
    $settings['exclude_node_title'] = [
      '#type' => 'select',
      '#title' => $this->t('Use Exclude Node Title'),
      '#options' => ['No', 'Yes'],
      '#description' => $this->t('Use the settings for the Exclude Node Title module for the title. Set to "off" to always show title.'),
      '#default_value' => $config['exclude_node_title'],
    ];

    return $settings;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary($settings): array {
    $summary = Title::settingsSummary($settings);

    $config = $this->getConfiguration();
    if (!empty($config['exclude_node_title'])) {
      $summary[] = $this->t('Use Exclude Node Title: yes');
    }
    else {
      $summary[] = $this->t('Use Exclude Node Title: no');
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    $configuration = Title::defaultConfiguration();

    $configuration['exclude_node_title'] = 1;

    return $configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    $config = $this->getConfiguration();

    if ($config['exclude_node_title']) {
      $exclude_manager = $this->exclusionManager;
      if ($exclude_manager->isTitleExcluded($this->entity(), $this->viewMode())) {
        return [
          '#markup' => '',
        ];
      }
    }

    return Field::build();
  }

}
