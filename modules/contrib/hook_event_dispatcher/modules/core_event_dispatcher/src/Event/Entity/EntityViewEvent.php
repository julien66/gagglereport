<?php

namespace Drupal\core_event_dispatcher\Event\Entity;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\core_event_dispatcher\EntityHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;

/**
 * Class EntityViewEvent.
 */
#[HookEvent(id: 'entity_view', hook: 'entity_view')]
class EntityViewEvent extends AbstractEntityEvent {

  /**
   * A renderable array representing the entity content.
   *
   * @var array
   */
  private array $build = [];

  /**
   * EntityViewEvent constructor.
   *
   * @param array &$build
   *   A renderable array representing the entity content. The module may add
   *   elements to $build prior to rendering. The structure of $build is a
   *   renderable array as expected by drupal_render().
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity object.
   * @param \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display
   *   The entity view display holding the display options configured for the
   *   entity components.
   * @param string $viewMode
   *   The view mode the entity is rendered in.
   */
  public function __construct(
    array &$build,
    EntityInterface $entity,
    private readonly EntityViewDisplayInterface $display,
    private readonly string $viewMode,
  ) {
    parent::__construct($entity);

    $this->build = &$build;
  }

  /**
   * Get the build.
   *
   * @return array
   *   The build.
   */
  public function &getBuild(): array {
    return $this->build;
  }

  /**
   * Get the display.
   *
   * @return \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   *   The display.
   */
  public function getDisplay(): EntityViewDisplayInterface {
    return $this->display;
  }

  /**
   * Get the view mode.
   *
   * @return string
   *   The view mode.
   */
  public function getViewMode(): string {
    return $this->viewMode;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return EntityHookEvents::ENTITY_VIEW;
  }

}
