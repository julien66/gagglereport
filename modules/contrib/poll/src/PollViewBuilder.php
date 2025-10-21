<?php

namespace Drupal\poll;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Render controller for polls.
 */
class PollViewBuilder extends EntityViewBuilder {

  /**
   * The request stack.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected RequestStack $requestStack;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    $instance = parent::createInstance($container, $entity_type);
    $instance->requestStack = $container->get('request_stack');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $entity = $this->entityRepository->getTranslationFromContext($entity, $langcode);

    // Ajax request might send the view mode as a GET argument, use that
    // instead.
    if ($this->requestStack->getCurrentRequest()->query->has('view_mode')) {
      $view_mode = $this->requestStack->getCurrentRequest()->query->get('view_mode');
    }

    $output = parent::view($entity, $view_mode, $langcode);

    $output['#poll'] = $entity;
    $output['poll_votes'] = [
      '#lazy_builder' => [
        'poll.post_render_cache:renderViewForm',
        [
          'id' => $entity->id(),
          'view_mode' => $view_mode,
          'langcode' => $entity->language()->getId(),
        ],
      ],
      '#create_placeholder' => TRUE,
      '#cache' => [
        'tags' => $entity->getCacheTags(),
      ],
    ];

    return $output;

  }

}
