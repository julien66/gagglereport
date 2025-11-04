<?php

namespace Drupal\bootstrap_flag_icons\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\language\Plugin\Block\LanguageBlock;

/**
 * Provides an example block.
 */
#[Block(
  id: "bootstrap_flag_icons_block",
  admin_label: new TranslatableMarkup("Bootstrap Language switcher"),
  category: new TranslatableMarkup("System"),
  deriver: 'Drupal\bootstrap_flag_icons\Plugin\Derivative\BootstrapFlagIconsBlock'
)]
class BootstrapFlagIconsBlock extends LanguageBlock {

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);

    $config = $this->getConfiguration();
    $settings = $config['bootstrap_language'] ?? [];

    $form['bootstrap_language'] = [
      '#type' => 'details',
      '#title' => $this->t('Bootstrap settings'),
      '#open' => TRUE,
    ];

    $form['bootstrap_language']['dropdown_style'] = [
      '#type' => 'select',
      '#title' => $this->t('Dropdown display style'),
      '#options' => [
        'all' => $this->t('Icons and text'),
        'icons' => $this->t('Only icons'),
      ],
      '#default_value' => !empty($settings['dropdown_style']) ? $settings['dropdown_style'] : 'all',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $values = $form_state->getValues();
    $this->configuration['bootstrap_language'] = $values['bootstrap_language'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    if (!empty($build['#links'])) {
      $settings = $this->configuration['bootstrap_language'];
      $build['#theme'] = 'links__bootstrap_flag_icons_block';
      foreach ($build['#links'] as &$langLinks) {
        $langLinks["attributes"]['data-mode'] = $settings['dropdown_style'];
      }
      $build['#attributes']['class'][] = 'dropdown';
      if (!empty($settings['dropdown_style'])) {
        $build['#attributes']['class'][] = "{$settings['dropdown_style']}-dropdown-style";
      }
      else {
        $build['#attributes']['class'][] = 'all-dropdown-style';
      }
      $build['#attached']['library'][] = 'bootstrap_flag_icons/bootstrap_flag_icons';
    }
    return $build;
  }

}
