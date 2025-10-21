<?php

namespace Drupal\cookies\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class CallbackController.
 *
 * THIS IS JUST A DEMO
 * This controller has no use for the functionality of this module.
 * Writes log message that user changed cookie configuration.
 * You can try it, when you activate the callback function
 * in the "Base Config"( /admin/config/system/cookies/config ) of this module.
 */
class CallbackController extends ControllerBase {

  /**
   * Drupal\Core\Messenger\MessengerInterface definition.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Famous Logger Channel Factory.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactory
   */
  protected $logger;


  /**
   * Famous Logger Channel Factory.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Drupal\Core\Messenger\MessengerInterface definition.
   *
   * @var \Symfony\Component\HttpFoundation\Request
   */
  protected $request;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $instance = parent::create($container);
    $instance->request = $container->get('request_stack')->getCurrentRequest();
    $instance->logger = $container->get('logger.factory');
    $instance->moduleHandler = $container->get('module_handler');
    return $instance;
  }

  /**
   * Callback.
   *
   * @return string
   *   Return Hello string.
   */
  public function callback() {
    $consent = ($this->request->getMethod() == 'POST')
      ? json_decode($this->request->getContent(), TRUE)
      : $this->request->query->all();

    $feedback = $this->moduleHandler
      ->invokeAll('cookies_user_consent', [$consent]);

    return new JsonResponse($feedback);
  }

}
