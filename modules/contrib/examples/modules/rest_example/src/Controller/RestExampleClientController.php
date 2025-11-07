<?php

namespace Drupal\rest_example\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Link;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\rest_example\RestExampleClientCalls;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller class for the REST Example routes.
 *
 * @ingroup rest_example
 */
class RestExampleClientController implements ContainerInjectionInterface {

  use MessengerTrait;
  use StringTranslationTrait;

  /**
   * The config factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
  */
  protected $configFactory;

  /**
   * The service to make REST calls.
   *
   * @var \Drupal\rest_example\RestExampleClientCalls
   */
  protected $restClient;

  /**
   * Constructs a new \Drupal\rest_example\Controller\RestExampleClientController object.
   *
   * @param \Drupal\rest_example\RestExampleClientCalls $rest_client
   *   The service to make REST calls.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    RestExampleClientCalls $rest_client,
  ) {
    $this->configFactory = $config_factory;
    $this->restClient = $rest_client;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    $controller = new static(
      $container->get('config.factory'),
      $container->get('rest_example_client_calls')
    );

    $controller->setMessenger($container->get('messenger'));
    $controller->setStringTranslation($container->get('string_translation'));

    return $controller;
  }

  /**
   * Retrieves the list of all nodes available on the remote site.
   *
   * Building the list as a table by calling the RestExampleClientCalls::index()
   * and builds the list from the response of that.
   *
   * @throws \RuntimeException
   * @throws \GuzzleHttp\Exception\GuzzleException
   */
  public function indexAction() {
    $build['intro'] = [
      '#markup' => $this->t('This is a list of nodes, of type <em>Rest Example Test</em>, on the remote server. From here you can create new node, edit and delete existing ones.'),
    ];

    $build['node_table'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Title'),
        $this->t('Type'),
        $this->t('Created'),
        $this->t('Edit'),
        $this->t('Delete'),
      ],
      '#empty' => $this->t('There are no items on the remote system yet'),
    ];

    if ($this->configFactory->get('rest_example.settings')->get('server_url')) {
      $this->messenger()->addWarning(
        $this->t('The remote endpoint service address has not been set. Please go and provide the credentials and the endpoint address on the <a href=":url">config page</a>.', [':url' => base_path() . 'examples/rest-client-settings'])
      );
    }
    else {
      $nodes = $this->restClient->index();

      if (!empty($nodes)) {
        foreach ($nodes as $delta => $node) {
          $build['node_table'][$delta]['title']['#plain_text'] = $node['title'];
          $build['node_table'][$delta]['type']['#plain_text'] = $node['type'];
          $build['node_table'][$delta]['created']['#plain_text'] = $node['created'];
          $build['node_table'][$delta]['edit']['#plain_text'] = Link::createFromRoute($this->t('Edit'), 'rest_example.client_actions_edit', ['id' => $node['nid']])->toString();
          $build['node_table'][$delta]['delete']['#plain_tex'] = Link::createFromRoute($this->t('Delete'), 'rest_example.client_actions_delete', ['id' => $node['nid']])->toString();
        }
      }

    }

    return $build;
  }

}
