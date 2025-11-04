<?php

namespace Drupal\Tests\views_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Drupal\views_event_dispatcher\Event\Views\ViewsDataAlterEvent;
use Drupal\views_event_dispatcher\Event\Views\ViewsDataEvent;
use Drupal\views_event_dispatcher\ViewsHookEvents;

/**
 * Class ViewDataEventTest.
 *
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsDataAlterEvent
 * @covers \Drupal\views_event_dispatcher\Event\Views\ViewsDataEvent
 *
 * @group hook_event_dispatcher
 * @group views_event_dispatcher
 */
class ViewDataEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'views',
    'hook_event_dispatcher',
    'views_event_dispatcher',
  ];

  /**
   * ViewsDataEvent test.
   *
   * @throws \Exception
   */
  public function testViewsDataEvent(): void {
    $this->listen(ViewsHookEvents::VIEWS_DATA, 'onViewsData');
    $this->listen(ViewsHookEvents::VIEWS_DATA_ALTER, 'onViewsDataAlter');

    $data = $this->container->get('views.views_data')->getAll();

    $this->assertArrayHasKey('test', $data);
    $this->assertEquals([
      0 => 'test_array_data',
      1 => 'other_test_array_data',
      'other_test' => ['some_data'],
    ], $data['test']);

    $this->assertArrayHasKey('some', $data);
    $this->assertEquals(['other_data'], $data['some']);
  }

  /**
   * Callback for ViewsDataEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsDataEvent $event
   *   The event.
   */
  public function onViewsData(ViewsDataEvent $event): void {
    $event->addData([
      'test' => [
        'test_array_data',
      ],
    ]);
    $event->addData([
      'test' => [
        'other_test_array_data',
      ],
    ]);
    $event->addData([
      'some' => [
        'other_data',
      ],
    ]);
  }

  /**
   * Callback for ViewsDataAlterEvent.
   *
   * @param \Drupal\views_event_dispatcher\Event\Views\ViewsDataAlterEvent $event
   *   The event.
   */
  public function onViewsDataAlter(ViewsDataAlterEvent $event): void {
    $data = &$event->getData();
    $data['test']['other_test'] = ['some_data'];
  }

}
