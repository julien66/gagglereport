<?php

namespace Drupal\hook_event_dispatcher\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;

/**
 * Defines hook_event attribute object.
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class HookEvent extends Plugin {

  /**
   * {@inheritdoc}
   */
  public function __construct(
    string $id,
    public readonly ?string $hook = NULL,
    public readonly ?string $alter = NULL,
    ?string $deriver = NULL,
  ) {
    parent::__construct($id, $deriver);
  }

}
