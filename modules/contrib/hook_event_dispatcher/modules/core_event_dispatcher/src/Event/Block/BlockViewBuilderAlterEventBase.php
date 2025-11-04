<?php

namespace Drupal\core_event_dispatcher\Event\Block;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class BlockViewBuilderAlterEventBase.
 */
abstract class BlockViewBuilderAlterEventBase extends Event implements EventInterface {

  /**
   * The renderable array of build data.
   *
   * @var array
   */
  protected $build = [];

  /**
   * BlockBuildAlterEvent constructor.
   *
   * @param array $build
   *   A renderable array of build data.
   * @param \Drupal\Core\Block\BlockPluginInterface $block
   *   The block plugin instance.
   */
  public function __construct(array &$build, protected readonly BlockPluginInterface $block) {
    $this->build = &$build;
  }

  /**
   * Gets the renderable array of build data.
   *
   * @return array
   *   The renderable array of build data.
   */
  public function &getBuild(): array {
    return $this->build;
  }

  /**
   * Gets the block plugin instance.
   *
   * @return \Drupal\Core\Block\BlockPluginInterface
   *   The block plugin instance.
   */
  public function getBlock(): BlockPluginInterface {
    return $this->block;
  }

}
