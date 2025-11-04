<?php

namespace Drupal\core_event_dispatcher\Event\Theme;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Asset\AttachedAssetsInterface;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class JsAlterEvent.
 */
#[HookEvent(id: 'js_alter', alter: 'js')]
final class JsAlterEvent extends Event implements EventInterface {

  /**
   * Javascript.
   *
   * @var array
   */
  private array $javascript = [];

  /**
   * JsAlterEvent constructor.
   *
   * @param array $javascript
   *   Javascript.
   * @param \Drupal\Core\Asset\AttachedAssetsInterface $attachedAssets
   *   AttachedAssets.
   */
  public function __construct(
    array &$javascript,
    private readonly AttachedAssetsInterface $attachedAssets,
  ) {
    $this->javascript = &$javascript;
  }

  /**
   * Get the javascript.
   *
   * @return array
   *   Javascript.
   */
  public function &getJavascript(): array {
    return $this->javascript;
  }

  /**
   * Get the attached assets.
   *
   * @return \Drupal\Core\Asset\AttachedAssetsInterface
   *   AttachedAssets.
   */
  public function getAttachedAssets(): AttachedAssetsInterface {
    return $this->attachedAssets;
  }

  /**
   * Get the dispatcher type.
   *
   * @return string
   *   The dispatcher type.
   */
  public function getDispatcherType(): string {
    return ThemeHookEvents::JS_ALTER;
  }

}
