<?php

namespace Drupal\social_auth_facebook\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\social_auth\Form\SocialAuthSettingsForm;
use Drupal\social_auth\Plugin\Network\NetworkInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Settings form for Social Auth Facebook.
 */
class FacebookAuthSettingsForm extends SocialAuthSettingsForm {

  /**
   * The request context.
   *
   * @var \Drupal\Core\Routing\RequestContext
   */
  protected RequestContext $requestContext;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container): static {
    $instance = parent::create($container);
    $instance->requestContext = $container->get('router.request_context');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'social_auth_facebook_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return array_merge(
      parent::getEditableConfigNames(),
      ['social_auth_facebook.settings']
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ?NetworkInterface $network = NULL): array {
    /** @var \Drupal\social_auth\Plugin\Network\NetworkInterface $network */
    $network = $this->networkManager->createInstance('social_auth_facebook');
    $form = parent::buildForm($form, $form_state, $network);

    $config = $this->config('social_auth_facebook.settings');

    $form['network']['client_id']['#title'] = $this->t('App ID');
    $form['network']['client_secret']['#title'] = $this->t('App secret');

    $form['network']['#description'] =
      $this->t('You need to first create a Facebook App at <a href="@facebook-dev">@facebook-dev</a>', ['@facebook-dev' => 'https://developers.facebook.com/apps']
    );

    $form['network']['graph_version'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Facebook Graph API version'),
      '#default_value' => $config->get('graph_version'),
      '#description' => $this->t('Copy the API Version of your Facebook App here. This value can be found from your App Dashboard. More information on API versions can be found at <a href="@facebook-changelog">Facebook Platform Changelog</a>', ['@facebook-changelog' => 'https://developers.facebook.com/docs/apps/changelog']),
    ];

    $form['network']['advanced']['#weight'] = 999;

    $form['network']['advanced']['scopes']['#description'] =
      $this->t('Define any additional scopes to be requested, separated by a comma (e.g.: user_birthday,user_location).<br>
                The scopes \'email\' and \'public_profile\' are added by default and always requested.<br>
                You can see the full list of valid scopes and their description <a href="@scopes">here</a>.', ['@scopes' => 'https://developers.facebook.com/docs/facebook-login/permissions/']
    );

    $form['network']['advanced']['endpoints']['#description'] =
      $this->t('Define the Endpoints to be requested when user authenticates with Facebook for the first time<br>
                Enter each endpoint in different lines in the format <em>endpoint</em>|<em>name_of_endpoint</em>.<br>
                <b>For instance:</b><br>
                /me?fields=birthday|user_birthday<br>
                /me?fields=address|user_address'
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    $graph_version = $form_state->getValue('graph_version');
    if ($graph_version[0] === 'v') {
      $graph_version = substr($graph_version, 1);
      $form_state->setValue('graph_version', $graph_version);
    }
    if (!preg_match('/^([2-9]|[1-9][0-9])\.[0-9]{1,2}$/', $graph_version)) {
      $form_state->setErrorByName('graph_version', $this->t('Invalid API version. The syntax for API version is for example <em>v2.8</em>'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $values = $form_state->getValues();
    $this->config('social_auth_facebook.settings')
      ->set('graph_version', $values['graph_version'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
