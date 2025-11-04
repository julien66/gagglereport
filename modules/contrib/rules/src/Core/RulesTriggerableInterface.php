<?php

namespace Drupal\rules\Core;

// cspell:ignore triggerable

/**
 * Interface for objects that are triggerable.
 */
interface RulesTriggerableInterface {

  /**
   * Gets configuration of all events the rule is reacting on.
   *
   * @return array
   *   The events array. The array is numerically indexed and contains arrays
   *   with the following structure:
   *   - event_name: String with the event machine name.
   *   - configuration: An array containing the event configuration.
   */
  public function getEvents();

  /**
   * Gets machine names of all events the rule is reacting on.
   *
   * @return string[]
   *   The array of fully qualified event names of the rule.
   */
  public function getEventNames();

  /**
   * Adds an event to the rule configuration.
   *
   * @param string $event_name
   *   The machine name of the event to add.
   * @param array $configuration
   *   (optional) Configuration of the event.
   *
   * @return \Drupal\rules\Core\RulesTriggerableInterface
   *   The object instance itself, to allow chaining.
   */
  public function addEvent(string $event_name, array $configuration = []);

  /**
   * Returns if the rule is reacting on the given event.
   *
   * @param string $event_name
   *   The machine name of the event to look for.
   *
   * @return bool
   *   TRUE if the rule is reacting on the given event, FALSE otherwise.
   */
  public function hasEvent(string $event_name);

  /**
   * Removes an event from the rule configuration.
   *
   * @param string $event_name
   *   The name of the (configured) event to remove.
   *
   * @return \Drupal\rules\Core\RulesTriggerableInterface
   *   The object instance itself, to allow chaining.
   */
  public function removeEvent(string $event_name);

}
