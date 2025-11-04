<?php

namespace Drupal\mailchimp;

use Drupal\Core\Config\Config;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\mailchimp\Exception\ClientFactoryException;
use Mailchimp\MailchimpApiInterface;
use Mailchimp\MailchimpApiUser;
use Psr\Log\LoggerInterface;

/**
 * Factory for Mailchimp PHP Library.
 */
class ClientFactory {

  use StringTranslationTrait;
  /**
   * Mailchimp Settings.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected Config $config;

  /**
   * StateService.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected StateInterface $stateService;

  /**
   * Mailchimp logging interface.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected LoggerInterface $logger;

  /**
   * Messenger Service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected MessengerInterface $messenger;

  /**
   * Mailchimp Library instances, keyed by class name.
   *
   * @var array
   */
  protected $instances = [];

  /**
   * ClientFactory constructor.
   *
   * @param \Drupal\Core\Config\Config $config
   *   Mailchimp Settings.
   * @param \Psr\Log\LoggerInterface $logger
   *   Logging interface.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   Messenger Service.
   * @param \Drupal\Core\State\StateInterface $stateService
   *   State Service.
   */
  public function __construct(Config $config, LoggerInterface $logger, MessengerInterface $messenger, StateInterface $stateService) {
    $this->config = $config;
    $this->logger = $logger;
    $this->messenger = $messenger;
    $this->stateService = $stateService;
  }

  /**
   * Retrieve a Mailchimp Library class by classname.
   *
   * @param string $classname
   *   Relative class name for a Mailchimp Library object.
   *
   * @return \Mailchimp\MailchimpApiUser
   *   Mailchimp Library.
   *
   * @throws \Drupal\mailchimp\Exception\ClientFactoryException
   */
  public function getByClassName(string $classname = 'MailchimpApiUser'): MailchimpApiUser {
    return $this->getInstance($this->resolveClass($classname));
  }

  /**
   * Wrapper around getByClassName() to handle exceptions.
   *
   * @param string $classname
   *   Relative class name for a Mailchimp Library object.
   *
   * @return \Mailchimp\MailchimpApiUser|null
   *   Mailchimp Library or Null.
   */
  public function getByClassNameOrNull(string $classname = 'MailchimpApiUser') {
    try {
      return $this->getByClassName($classname);
    }
    catch (ClientFactoryException $e) {
      return NULL;
    }
  }

  /**
   * Returns an instance of the Mailchimp API class.
   *
   * @return \Mailchimp\MailchimpApiInterface
   *   Mailchimp Api Object.
   *
   * @throws \Drupal\mailchimp\Exception\ClientFactoryException
   */
  public function getApiClass(): MailchimpApiInterface {
    // If OAuth is enabled.
    if ($this->config->get('use_oauth')) {
      $api_class_name = '\Mailchimp\Mailchimp2';
      $authentication_settings = [
        'access_token' => $this->stateService->get('mailchimp_access_token'),
        'data_center' => $this->stateService->get('mailchimp_data_center'),
        'api_user' => 'oauth',
      ];
    }
    else {
      $api_class_name = $this->resolveClass('Mailchimp');
      $authentication_settings = [
        'api_key' => $this->config->get('api_key'),
        'api_user' => 'api_key',
      ];
    }

    $http_options = [
      'timeout' => $this->config->get('api_timeout'),
      'headers' => [
        'User-Agent' => _mailchimp_get_user_agent(),
      ],
    ];

    $http_client_config = Settings::get('http_client_config', []);
    if (isset($http_client_config['proxy'])) {
      $http_options['proxy'] = $http_client_config['proxy'];
    }

    $api_class = new $api_class_name($authentication_settings, $http_options);

    if (!isset($api_class)) {
      $this->logger->error('Mailchimp Authentication values cannot be blank.');
      $this->messenger->addError($this->t('Mailchimp Authentication values are needed for functionality to work.'));
      throw new ClientFactoryException('Mailchimp Authentication values cannot be blank');
    }

    return $api_class;
  }

  /**
   * Loads an instance of a Mailchimp Api User object, creating if necessary.
   *
   * @param string $class
   *   Explicit class name for a Mailchimp Library object.
   *
   * @return \Mailchimp\MailchimpApiUser
   *   Mailchimp Library.
   *
   * @throws \Drupal\mailchimp\Exception\ClientFactoryException
   */
  protected function getInstance(string $class): MailchimpApiUser {
    if (!isset($this->instances[$class])) {
      $this->instances[$class] = $this->createInstance($class);
    }

    return $this->instances[$class];
  }

  /**
   * Instantiates a new instance of a Mailchimp API User class.
   *
   * @param string $class
   *   Explicit class name for a Mailchimp Library object.
   *
   * @return \Mailchimp\MailchimpApiUser
   *   Mailchimp ApiUser Object.
   *
   * @throws \Drupal\mailchimp\Exception\ClientFactoryException
   */
  protected function createInstance(string $class): MailchimpApiUser {
    return new $class($this->getApiClass());
  }

  /**
   * Classname autoloader to enforce test mode when configured.
   *
   * @param string $classname
   *   Relative class name for a Mailchimp Library object.
   *
   * @return string
   *   Explicit class name.
   */
  protected function resolveClass(string $classname): string {
    if ($this->config->get('test_mode')) {
      // Register autoloader for loading test classes.
      Autoload::register();
      $classname = '\Mailchimp\Tests\\' . $classname;
    }
    else {
      $classname = '\Mailchimp\\' . $classname;
    }

    return $classname;
  }

}
