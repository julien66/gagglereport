<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\Component\EventDispatcher\Event;
use Drupal\Core\Config\ConfigImporter;
use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class ConfigImportStepsAlterEvent.
 */
#[HookEvent(id: 'config_import_steps_alter', alter: 'config_import_steps')]
class ConfigImportStepsAlterEvent extends Event implements EventInterface {

  /**
   * The configuration synchronization steps.
   *
   * @var array
   */
  protected $syncSteps = [];

  /**
   * ConfigImportStepsAlterEvent constructor.
   *
   * @param array $syncSteps
   *   A one-dimensional array of \Drupal\Core\Config\ConfigImporter method
   *   names or callables that are invoked to complete the import, in the order
   *   that they will be processed. Each callable item defined in $syncSteps
   *   should either be a global function or a public static method. The
   *   callable should accept a $context array by reference.
   * @param \Drupal\Core\Config\ConfigImporter $configImporter
   *   The config importer.
   */
  public function __construct(array &$syncSteps, protected readonly ConfigImporter $configImporter) {
    $this->syncSteps = &$syncSteps;
  }

  /**
   * Gets the configuration synchronization steps.
   *
   * @return array
   *   The configuration synchronization steps.
   */
  public function &getSyncSteps(): array {
    return $this->syncSteps;
  }

  /**
   * Gets the config importer.
   *
   * @return \Drupal\Core\Config\ConfigImporter
   *   The config importer.
   */
  public function getConfigImporter(): ConfigImporter {
    return $this->configImporter;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::CONFIG_IMPORT_STEPS_ALTER;
  }

}
