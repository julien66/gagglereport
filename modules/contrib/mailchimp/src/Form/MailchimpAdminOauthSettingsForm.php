<?php

namespace Drupal\mailchimp\Form;

use Drupal\Core\Access\CsrfTokenGenerator;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\MessageCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\State;
use Drupal\mailchimp\Ajax\MailchimpAuthenticationCommand;
use Drupal\mailchimp\Controller\MailchimpOAuthController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Mailchimp\MailchimpAPIException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Mailchimp OAuth settings for this site.
 */
class MailchimpAdminOauthSettingsForm extends ConfigFormBase {

  /**
   * StateService.
   *
   * @var Drupal\Core\State
   */
  protected State $stateService;

  /**
   * CsrfService.
   *
   * @var \Drupal\Core\Access\CsrfTokenGenerator
   */
  protected CsrfTokenGenerator $csrfService;

  /**
   * Mailchimp OAuth Settings form constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   Config factory.
   * @param \Drupal\Core\Config\TypedConfigManagerInterface $typedConfigManager
   *   The typed config manager.
   * @param \Drupal\Core\State\State $stateService
   *   State service.
   * @param \Drupal\Core\Access\CsrfTokenGenerator $csrfService
   *   CSRF service.
   */
  public function __construct(ConfigFactoryInterface $configFactory, TypedConfigManagerInterface $typedConfigManager, State $stateService, CsrfTokenGenerator $csrfService) {
    parent::__construct($configFactory, $typedConfigManager);
    $this->stateService = $stateService;
    $this->csrfService = $csrfService;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('state'),
      $container->get('csrf_token'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'mailchimp_admin_oauth_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['mailchimp.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $button_action = $this->t('Authenticate');

    // If status is accepted, trigger the getAccessToken method.
    if (isset($_GET['status']) && $_GET['status'] == 'accepted') {
      $this->getAccessToken($_GET['temp_token']);
    }

    // Check for an existing auth token.
    $access_token = $this->stateService->get('mailchimp_access_token');
    $data_center = $this->stateService->get('mailchimp_data_center');
    if ($access_token && $data_center) {
      try {
        $mc_api = mailchimp_get_api_object();
        if ($mc_api !== NULL) {
          $account = $mc_api->getAccount();
        }
        else {
          $account = NULL;
        }
        if ($account) {
          $button_action = 'Re-Authenticate';
          $this->messenger()->addStatus('Authentication complete! Successfully connected to Mailchimp API.');
        }
      }
      catch (MailchimpAPIException $e) {
        $this->messenger()->addWarning('There is a problem with your authentication. Click "Re-Authenticate" below and try again.');
      }
    }
    if (!$this->config('mailchimp.settings')->get('use_oauth')) {
      $this->messenger()->addWarning('Your site is still set up to use an API key and that method of authentication is deprecated. Select "Use OAuth Authentication" under Global Settings, save, and then return here to set up OAuth.');
    }

    $form['status'] = [
      '#type' => 'markup',
      '#markup' => '<div id="mailchimp-authentication-status"></div>',
    ];

    $form['domain'] = [
      '#type' => 'textfield',
      '#required' => TRUE,
      '#title' => $this->t('Website domain'),
      '#description' => $this->t('Enter the domain you want to authenticate. Example: mydrupalsite.com'),
      '#default_value' => !empty($this->config('mailchimp.settings')->get('domain')) ? $this->config('mailchimp.settings')->get('domain') : $_SERVER['HTTP_HOST'],
    ];

    $form['#attached']['library'][] = 'mailchimp/authentication';
    $form['#attached']['drupalSettings']['mailchimp']['middleware_url'] = $this->getMiddlewareUrl();
    $form['#attached']['drupalSettings']['mailchimp']['csrf_token'] = $this->csrfService->get("mailchimp_admin_oauth_settings");

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $button_action,
      '#ajax' => [
        'callback' => '::authenticate',
      ],
      '#suffix' => "<span class='spinner'></span>",
    ];

    return $form;
  }

  /**
   * AJAX trigger for authentication sequence with Middleware server.
   *
   * @param array $form
   *   Form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   FormState object.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   An Ajax response.
   */
  public function authenticate(array $form, FormStateInterface $form_state) {
    $client = $this->client();
    $domain = $form_state->getValue('domain');

    $post_params = [
      'form_params' => [
        'domain' => $domain,
      ],
    ];

    // Make inital POST call to middleware server.
    $response = $client->request('POST', $this->getMiddlewareUrl() . '/init', $post_params);

    if ($response->getStatusCode() == '200') {
      // Check for a token in the response.
      $json = $response->getBody()->getContents();
      $message = json_decode($json, TRUE);

      // If a token is returned.
      if (isset($message['temp_token'])) {
        // Save the submitted username and temp_token.
        $config = $this->config('mailchimp.settings');
        $config->set('domain', $domain)->save();

        $temp_token = $message['temp_token'];

        // Authenticate.
        // If successful trigger authentication in new window.
        $get_params = [
          'temp_token' => $temp_token,
          'login' => TRUE,
        ];
        // @todo update with middleware location in GCP.
        $url = $this->getMiddlewareUrl() . '/oauth/mailchimp/callback?' . http_build_query($get_params);

        $response = new AjaxResponse();
        $response->addCommand(
          new MailchimpAuthenticationCommand($url, $temp_token, $domain)
        );
        $pending_message = 'An authentication request has been opened in a new window. Please follow the prompts to login and grant Drupal access to your Mailchimp account. NOTE: a pop-up blocker might prevent this action.';
        $response->addCommand(new MessageCommand($pending_message, '#mailchimp-authentication-status', ['type' => 'warning']));
        return $response;
      }
      else {
        $response = new AjaxResponse();
        $pending_message = 'There was an error connecting to the authentication server. Please try again later.';
        $response->addCommand(new MessageCommand($pending_message, '#mailchimp-authentication-status', ['type' => 'error']));
        return $response;
      }
    }
  }

  /**
   * Request access token from middleware server.
   *
   * @param string $temp_token
   *   The temporary token that was issued from the initial request.
   */
  protected function getAccessToken($temp_token) {
    $post_params = [
      'form_params' => [
        'type' => 'access_token',
        'temp_token' => $temp_token,
      ],
    ];
    $client = $this->client();
    try {
      $response = $client->request('POST', $this->getMiddlewareUrl() . '/access-token', $post_params);
      // Check for response with access_token, and store in database.
      if ($response->getStatusCode() == '200') {
        // Check for a token in the response.
        $json = $response->getBody()->getContents();
        $message = json_decode($json, TRUE);

        if (isset($message['access_token'])) {
          $access_token = $message['access_token'];
          $data_center = $message['data_center'];
          $config = $this->config('mailchimp.settings');
          $config
            ->set('access_token', $access_token)
            ->set('data_center', $data_center)
            ->save();
        }
      }
      else {
        // @todo update to display actual error messages.
        $this->messenger()->addError('There was an error communicating with the authentication server.');
        return;
      }
    }
    catch (GuzzleException $e) {
      $this->messenger()->addError($e->getMessage());
    }
  }

  /**
   * Initialize a new Guzzle client.
   *
   * @return \GuzzleHttp\Client
   *   Guzzle client.
   */
  protected function client() {
    // Intialize Guzzle Client.
    return new Client([
      'base_uri' => '',
      // @todo is this a realistic timeout?
      'timeout'  => 100.0,
    ]);
  }

  /**
   * Get the Drupal Mailchimp Middleware Url.
   *
   * @return string
   *   Middleware url.
   */
  protected function getMiddlewareUrl() {
    $url = MailchimpOAuthController::OAUTH_MIDDLEWARE_URL;
    return $url;
  }

}
