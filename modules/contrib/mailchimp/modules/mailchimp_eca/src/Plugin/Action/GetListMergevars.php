<?php

namespace Drupal\mailchimp_eca\Plugin\Action;

use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\eca\Attribute\EcaAction;
use Drupal\eca\Plugin\Action\ConfigurableActionBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Describes the mailchimp_eca mailchimp_eca_get_list_mergevars action.
 */
#[Action(
  id: 'mailchimp_eca_get_list_mergevars',
  label: new TranslatableMarkup('Get signup list mergevars'),
)]
#[EcaAction(
  description: new TranslatableMarkup('Get the public mergevars for a Mailchimp signup list.'),
  version_introduced: '2.1.12',
)]
class GetListMergevars extends ConfigurableActionBase {

  /**
   * The Mailchimp API service.
   *
   * @var \Mailchimp\MailchimpApiInterface
   */
  protected $apiService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id, $plugin_definition);
    $instance->apiService = $container->get('mailchimp.api');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(): void {
    $mergevar_configuration = [];
    $list_id = $this->tokenService->replace($this->configuration['list_id']);
    $merge_vars = current($this->apiService->getMergeVars([$list_id], TRUE));
    foreach ($merge_vars as $key => $var) {
      if (isset($var->public) && $var->public) {
        if ($var->required) {
          $mergevar_configuration[] = [
            'tag' => $var->tag,
            'config' => serialize($var),
            'enabled' => TRUE,
          ];
        }
        else {
          $mergevar_configuration[] = [
            'tag' => $var->tag,
            'config' => $this->configuration['required_only'] ? 0 : serialize($var),
            'enabled' => !$this->configuration['required_only'],
          ];
        }
      }
    }
    $this->tokenService->addTokenData($this->configuration['token_name'], $mergevar_configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'list_id' => '',
      'required_only' => FALSE,
      'token_name' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $configuration = $this->getConfiguration();
    $form['list_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('List/Audience ID'),
      '#description' => $this->t('The Mailchimp API ID for the list to check mergevars.'),
      '#default_value' => $configuration['list_id'],
      '#required' => TRUE,
    ];
    $form['required_only'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Exclude optional mergevars'),
      '#description' => $this->t('Only enable required mergevars in our token.'),
      '#default_value' => $configuration['required_only'],
    ];
    $form['token_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of token'),
      '#description' => $this->t('Provide the name of a token that holds the mergevar names.'),
      '#default_value' => $configuration['token_name'],
      '#required' => TRUE,
      '#eca_token_reference' => TRUE,
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['list_id'] = $form_state->getValue('list_id');
    $this->configuration['required_only'] = $form_state->getValue('required_only');
    $this->configuration['token_name'] = $form_state->getValue('token_name');
    parent::submitConfigurationForm($form, $form_state);
  }

}
