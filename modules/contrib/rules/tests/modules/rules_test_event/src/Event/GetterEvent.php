<?php

namespace Drupal\rules_test_event\Event;

use Drupal\Component\EventDispatcher\Event;

/**
 * An Event that has getter methods defined for its properties.
 *
 * "Getter" events expose their properties through getter methods. Rules learns
 * about these methods through the 'getter' metadata declared in a
 * MODULE.rules.events.yml file. This allows Rules to access event properties
 * even if the getter methods don't follow the naming convention of
 * get<PropertyName>().
 *
 * This class is meant for testing, to ensure Rules can access properties using
 * the methods declared in the 'getter' metadata. A 'real' event class would
 * also have a constructor and/or setter methods to set the initial values of
 * the properties, and might have other methods that make use of these
 * properties to return a value.
 *
 * @see \Drupal\rules\EventSubscriber\GenericEventSubscriber
 */
class GetterEvent extends Event {

  const EVENT_NAME = 'rules_test_event.getter_event';

  /**
   * A public property.
   *
   * @var string
   */
  public $publicProperty = 'public property';

  /**
   * A protected property.
   *
   * @var string
   */
  protected $protectedProperty = 'protected property';

  /**
   * A private property.
   *
   * @var string
   */
  private $privateProperty = 'private property';

  /**
   * Getter method for $publicProperty.
   *
   * @return string
   *   The value of publicProperty.
   */
  public function publicGetter(): string {
    return $this->publicProperty;
  }

  /**
   * Getter method for $protectedProperty.
   *
   * @return string
   *   The value of protectedProperty.
   */
  public function protectedGetter(): string {
    return $this->protectedProperty;
  }

  /**
   * Getter method for $publicProperty.
   *
   * @return string
   *   The value of privateProperty.
   */
  public function privateGetter(): string {
    return $this->privateProperty;
  }

}
