<?php

namespace Drupal\httpswww\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Render\FormattableMarkup;

/**
 * The Class HttpsWwwSettingsForm.
 *
 * @package Drupal\httpswww\Form
 */
class HttpsWwwSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'httpswww_admin_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['httpswww.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('httpswww.settings');
    $host = preg_replace('/^www\./', '', \Drupal::request()->getHost());

    $replace_args = [
      '%host' => $host,
      '%wwwhost' => 'www.' . $host,
    ];

    $this->messenger()
      ->addWarning($this->t('Please read and understand the options before making any changes.'));

    $fieldset_1 = new FormattableMarkup('
      <ul>
         <li>@line_1</li>
         <li>@line_2</li>
         <li>@line_3</li>
      </ul>
      ', [
        '@line_1' => $this->t('Ensure your site is accessible for the chosen options before activating redirects.'),
        '@line_2' => $this->t('If you lack the %permission permission and are on a domain different from your selection, you will be logged out upon saving.', [
          '%permission' => $this->t('Bypass HTTPS and WWW Redirects'),
        ]),
        '@line_3' => $this->t('After activation, refrain from making additional modifications as it may negatively impact SEO (Search Engine Optimization).'),
      ]
    );

    $form['fieldset_1'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Redirect status'),
      '#description' => $this->t('@description', ['@description' => $fieldset_1]),
    ];

    $form['fieldset_1']['enabled'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable redirects'),
      '#default_value' => $config->get('enabled'),
    ];

    $form['fieldset_2'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Domain WWW prefix'),
    ];

    $form['fieldset_2']['prefix'] = [
      '#type' => 'radios',
      '#default_value' => $config->get('prefix') ?: 'mixed',
      '#options' => [
        'mixed' => $this->t('No redirect'),
        'no' => $this->t('Remove WWW prefix'),
        'yes' => $this->t('Add WWW prefix'),
      ],
      'mixed' => [
        '#description' => $this->t("Both %host and %wwwhost are accessible. This option is not recommended.", $replace_args),
      ],
      'no' => [
        '#description' => $this->t('Redirect %wwwhost to %host.', $replace_args),
      ],
      'yes' => [
        '#description' => $this->t('Redirect %host to %wwwhost.', $replace_args),
      ],
    ];

    $form['fieldset_2']['exclude_subdomains'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Exclude Subdomains'),
      '#description' => $this->t('Enter a comma separated list of subdomains that should not have the WWW prefix added. Example: forum, shop, support.'),
      '#default_value' => $config->get('exclude_subdomains') ? implode(', ', $config->get('exclude_subdomains')) : '',
      '#maxlength' => 255,
      '#size' => 120,
      '#states' => [
        'visible' => [
          ':input[name="prefix"]' => ['value' => 'yes'],
        ],
      ],
    ];

    $form['fieldset_3'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('HTTP Secure (HTTPS) redirect'),
    ];

    $https_desc = new FormattableMarkup('
      @line_1<br>
      @line_2<br>
      @line_3 <a href="@url" target="_blank">@url</a>
      ', [
        '@line_1' => $this->t('The site becomes accessible only via HTTPS. All HTTP requests are redirected to HTTPS.'),
        '@line_2' => $this->t('ATTENTION: If you choose this option, make sure your site is accessible by HTTPS, and that your SSL certificate is valid.'),
        '@line_3' => $this->t('For more information, see'),
        '@url' => 'https://www.drupal.org/https-information',
      ]
    );

    $form['fieldset_3']['scheme'] = [
      '#type' => 'radios',
      '#default_value' => $config->get('scheme') ?: 'mixed',
      '#options' => [
        'mixed' => $this->t('No redirect'),
        'https' => $this->t('Redirect to HTTPS'),
      ],
      'mixed' => [
        '#description' => $this->t('The site remains accessible via HTTP, as well as via HTTPS (if enabled).'),
      ],
      'https' => [
        '#description' => $this->t('@description', ['@description' => $https_desc]),
      ],
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = $this->config('httpswww.settings');
    $config->set('enabled', $form_state->getValue('enabled'));
    $config->set('prefix', $form_state->getValue('prefix'));
    $config->set('scheme', $form_state->getValue('scheme'));

    if ($exclude_subdomains = $form_state->getValue('exclude_subdomains')) {
      $subdomains = array_map('trim', explode(',', $exclude_subdomains));
      $subdomains = array_unique(array_filter($subdomains, 'strlen'));
      $config->set('exclude_subdomains', $subdomains);
    }

    $config->save();

    Cache::invalidateTags(['config:httpswww.settings']);

    $this->messenger()
      ->addStatus($this->t('The configuration options have been saved.'));
  }

}
