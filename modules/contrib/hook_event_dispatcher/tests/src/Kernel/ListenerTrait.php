<?php

namespace Drupal\Tests\hook_event_dispatcher\Kernel;

use PHPUnit\Framework\MockObject\Rule\InvocationOrder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * ListenerTrait.
 */
trait ListenerTrait {

  /**
   * The event dispatcher.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected EventDispatcherInterface $eventDispatcher;

  /**
   * Listens to event name with callable method.
   *
   * @param string|string[] $events
   *   The event name.
   * @param string $method
   *   The callable method.
   * @param \PHPUnit\Framework\MockObject\Rule\InvocationOrder|null $expects
   *   The mock object expectation.
   *
   * @throws \Exception
   */
  protected function listen($events, string $method, ?InvocationOrder $expects = NULL): void {
    if (!$expects) {
      $expects = $this->once();
    }

    $listener = $this->createMock(self::class);
    $listener->expects($expects)
      ->method($method)
      ->willReturnCallback([$this, $method]);

    if (is_string($events)) {
      $events = [$events];
    }

    foreach ($events as $event) {
      $this->doListen($event, [$listener, $method]);
    }
  }

  /**
   * Listens to event name with callback.
   *
   * @param string $eventName
   *   The event to listen on.
   * @param callable $callback
   *   The listener.
   *
   * @throws \Exception
   */
  private function doListen(string $eventName, callable $callback): void {
    $this->getEventDispatcher()->addListener($eventName, $callback);
  }

  /**
   * Get the event dispatcher service.
   *
   * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   The event dispatcher.
   *
   * @throws \Exception
   */
  public function getEventDispatcher(): EventDispatcherInterface {
    if (!isset($this->eventDispatcher)) {
      $this->eventDispatcher = $this->container->get('event_dispatcher');
    }

    return $this->eventDispatcher;
  }

}
