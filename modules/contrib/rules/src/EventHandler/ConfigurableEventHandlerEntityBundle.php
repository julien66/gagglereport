<?php

namespace Drupal\rules\EventHandler;

use Drupal\Core\Form\FormStateInterface;
use Drupal\rules\Event\EntityEvent;

/**
 * Exposes the bundle of an entity as event setting.
 */
class ConfigurableEventHandlerEntityBundle extends ConfigurableEventHandlerBase {

  /**
   * {@inheritdoc}
   */
  public static function determineQualifiedEvents(object $event, $event_name, array &$event_definition) {
    // @todo The 'object' type hint should be replaced with the appropriate
    // class once Symfony 4 is no longer supported.
    $events_suffixes = [];
    if ($event instanceof EntityEvent) {
      $events_suffixes[] = $event->getSubject()->bundle();
    }
    return $events_suffixes;
  }

  /**
   * {@inheritdoc}
   */
  public function summary() {
    // Nothing to do by default.
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Nothing to do by default.
  }

  /**
   * {@inheritdoc}
   */
  public function validate() {
    // Nothing to check by default.
  }

  /**
   * {@inheritdoc}
   */
  public function getEventNameSuffix() {
    // Nothing to do by default.
  }

  /**
   * {@inheritdoc}
   */
  public function refineContextDefinitions() {
    // Nothing to refine by default.
  }

}
