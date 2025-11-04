<?php

namespace Drupal\hook_event_dispatcher\Manager;

use Drupal\Component\EventDispatcher\Event;
use Drupal\hook_event_dispatcher\Event\EventInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class HookEventDispatcherManager.
 *
 * Wrapper class for the external dispatcher dependency. If this ever changes
 * we only have to change it once.
 */
class HookEventDispatcherManager implements HookEventDispatcherManagerInterface {

  /**
   * EntityDispatcherManager constructor.
   *
   * @param \Symfony\Contracts\EventDispatcher\EventDispatcherInterface $eventDispatcher
   *   The event dispatcher.
   */
  public function __construct(protected readonly EventDispatcherInterface $eventDispatcher) {
  }

  /**
   * {@inheritdoc}
   */
  public function register(EventInterface $event): Event {
    assert($event instanceof Event);
    if ($event->isPropagationStopped()) {
      return $event;
    }

    /** @var \Drupal\Component\EventDispatcher\Event */
    return $this->eventDispatcher->dispatch($event, $event->getDispatcherType());
  }

}
