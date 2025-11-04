<?php

namespace Drupal\mailchimp_eca\Plugin\Action;

use Drupal\Core\Action\Attribute\Action;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\eca\Attribute\EcaAction;
use Drupal\eca\Plugin\Action\ConfigurableActionBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Action plugin gets signup lists from Mailchimp and stores them in a token.
 */
#[Action(
  id: 'mailchimp_eca_get_signup_lists',
  label: new TranslatableMarkup('Get signup lists'),
)]
#[EcaAction(
  description: new TranslatableMarkup('Get the signup lists from Mailchimp.'),
  version_introduced: '2.1.12',
)]
class GetSignupLists extends ConfigurableActionBase {

  /**
   * The Mailchimp API service.
   *
   * @var \Drupal\mailchimp\ApiService
   */
  protected $apiService;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    $instance = parent::create($container, $configuration, $plugin_id,$plugin_definition);
    $instance->apiService = $container->get('mailchimp.api');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function execute(): void {
    // The result from "mailchimp_get_lists" is a list of objects, that can't be
    // stored in a token. Therefore, this gets encoded and decoded so that we
    // get a list of arrays.
    $list = json_decode(json_encode($this->apiService->getAudiences()), TRUE);
    $this->tokenService->addTokenData($this->configuration['token_name'], $list);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'token_name' => '',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['token_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name of token'),
      '#description' => $this->t('Provide the name of a token that holds the received list.'),
      '#default_value' => $this->configuration['token_name'],
      '#required' => TRUE,
      '#eca_token_reference' => TRUE,
    ];
    return parent::buildConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    $this->configuration['token_name'] = $form_state->getValue('token_name');
    parent::submitConfigurationForm($form, $form_state);
  }

}
