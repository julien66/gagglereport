<?php

namespace Drupal\rules_test_event\Event;

use Drupal\Component\EventDispatcher\Event as DrupalEvent;

/**
 * An Event that has properties but no explicit getter methods.
 *
 * "Plain" events like this don't have getter methods and don't subclass
 * from GenericEvent so there is no way to directly access their non-public
 * properties unless special steps are taken.
 *
 * This class is meant for testing. A 'real' event class would also have a
 * constructor and/or setter methods to set the initial values of the
 * properties, and might have non-getter methods that make use of these
 * properties to return a value.
 *
 * @see \Drupal\rules\EventSubscriber\GenericEventSubscriber
 */
class PlainEvent extends DrupalEvent {

  const EVENT_NAME = 'rules_test_event.plain_event';

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

}
