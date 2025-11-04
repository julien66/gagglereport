<?php

namespace Drupal\Tests\preprocess_event_dispatcher\Unit\Helpers;

use Drupal\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use function count;
use function end;

/**
 * Class SpyEventDispatcher.
 */
final class SpyEventDispatcher implements EventDispatcherInterface {

  /**
   * Events keyed by event name.
   *
   * @var \Drupal\Component\EventDispatcher\Event[]
   */
  private array $events = [];

  /**
   * Event count.
   *
   * @var int
   */
  private int $count = 1;

  /**
   * Set the expected event count.
   *
   * @param int $count
   *   Event count.
   */
  public function setExpectedEventCount(int $count): void {
    $this->count = $count;
  }

  /**
   * Mocking an event dispatch, saving the event.
   *
   * {@inheritdoc}
   */
  public function dispatch(object $event, ?string $eventName = NULL): object {
    if (count($this->events) === $this->count) {
      throw new \BadMethodCallException(sprintf('SpyEventDispatcher got called more than %d time(s)', $this->count));
    }

    $this->events[$eventName] = $event;

    return $event;
  }

  /**
   * Get the last event name.
   *
   * @return string
   *   Last event name.
   */
  public function getLastEventName(): string {
    return array_key_last($this->events);
  }

  /**
   * Get the last event.
   *
   * @return \Drupal\Component\EventDispatcher\Event
   *   Last event.
   */
  public function getLastEvent(): Event {
    return end($this->events);
  }

  /**
   * Get the events keyed by event name.
   *
   * @return \Drupal\Component\EventDispatcher\Event[]
   *   Events keyed by event name.
   */
  public function getEvents(): array {
    return $this->events;
  }

  /**
   * Mock.
   *
   * {@inheritdoc}
   */
  public function addListener(string $eventName, callable $listener, int $priority = 0): void {
    throw new \BadMethodCallException('This spy does not support this call');
  }

  /**
   * Mock.
   *
   * {@inheritdoc}
   */
  public function addSubscriber(EventSubscriberInterface $subscriber): void {
    throw new \BadMethodCallException('This spy does not support this call');
  }

  /**
   * Mock.
   *
   * {@inheritdoc}
   */
  public function removeListener(string $eventName, callable $listener): void {
    throw new \BadMethodCallException('This spy does not support this call');
  }

  /**
   * Mock.
   *
   * {@inheritdoc}
   */
  public function removeSubscriber(EventSubscriberInterface $subscriber): void {
    throw new \BadMethodCallException('This spy does not support this call');
  }

  /**
   * Mock.
   *
   * {@inheritdoc}
   */
  public function getListeners(?string $eventName = NULL): array {
    throw new \BadMethodCallException('This spy does not support this call');
  }

  /**
   * Mock.
   *
   * {@inheritdoc}
   */
  public function getListenerPriority(string $eventName, callable $listener): ?int {
    throw new \BadMethodCallException('This spy does not support this call');
  }

  /**
   * Mock.
   *
   * {@inheritdoc}
   */
  public function hasListeners(?string $eventName = NULL): bool {
    throw new \BadMethodCallException('This spy does not support this call');
  }

}
