<?php

namespace Drupal\poll\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Most recent poll' block.
 *
 * @Block(
 *   id = "poll_recent_block",
 *   admin_label = @Translation("Most recent poll"),
 *   category = @Translation("Lists (Views)")
 * )
 */
class PollRecentBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Entity repository service.
   *
   * @var \Drupal\Core\Entity\EntityRepositoryInterface
   */
  protected $entityRepository;

  /**
   * Construct a new PollRecentBlock object.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, EntityRepositoryInterface $entity_repository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entity_type_manager;
    $this->entityRepository = $entity_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('entity.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access polls');
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    return ['poll_list'];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $polls = $this->entityTypeManager->getStorage('poll')->getMostRecentPoll();
    if ($polls) {
      $poll = reset($polls);
      // @todo new view mode using ajax
      $build = $this->entityTypeManager->getViewBuilder('poll')->view($poll, 'block');
      $poll = $this->entityRepository->getTranslationFromContext($poll);
      $build['#title'] = $poll->label();
      // Added contextual link parameters.
      $build['#contextual_links'] = [
        'poll' => [
          'route_parameters' => ['poll' => $poll->id()],
        ],
      ];
    }

    return $build;
  }

}
