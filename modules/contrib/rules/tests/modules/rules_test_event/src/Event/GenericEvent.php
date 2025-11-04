<?php

namespace Drupal\rules_test_event\Event;

use Symfony\Component\EventDispatcher\GenericEvent as SymfonyGenericEvent;

/**
 * A subclass of Symfony's GenericEvent.
 *
 * "Generic" events are intended to be used for many purposes, as opposed to
 * using a hierarchy of specific event classes each meant for a specific
 * purpose. Generic events have their properties defined upon creation, by
 * passing them into the constructor as an associative array with property
 * names as keys and property values as values. Generic events expose their
 * properties through the GenericEvent::getArgument(string $property_name)
 * method. Because of this, we don't need to explicitly declare properties or
 * getter methods when subclassing GenericEvent.
 *
 * This class is meant for testing. GenericEvent is normally meant to be used
 * without needing to subclass it. A 'real' subclass would also define other
 * methods not inherited from GenericEvent, in order to add functionality to
 * GenericEvent. In this case, the only added functionality is to define an
 * EVENT_NAME.
 *
 * @see \Drupal\rules\EventSubscriber\GenericEventSubscriber
 */
class GenericEvent extends SymfonyGenericEvent {

  const EVENT_NAME = 'rules_test_event.generic_event';

}
