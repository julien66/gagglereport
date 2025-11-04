<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Menu;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\core_event_dispatcher\Event\Menu\MenuLocalTasksAlterEvent;
use Drupal\core_event_dispatcher\MenuHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class MenuLocalTasksAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Menu\MenuLocalTasksAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class MenuLocalTasksAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test MenuLocalTasksAlterEvent.
   *
   * @throws \Exception
   */
  public function testMenuLocalTasksAlterEvent(): void {
    $this->listen(MenuHookEvents::MENU_LOCAL_TASKS_ALTER, 'onMenuLocalTasksAlter', $this->exactly(2));

    $localTaskManager = $this->container->get('plugin.manager.menu.local_task');
    $noneLocalTasks = $localTaskManager->getLocalTasks('<none>');
    $this->assertArrayHasKey('tabs', $noneLocalTasks);
    $this->assertEmpty($noneLocalTasks['tabs']);

    $frontLocalTasks = $localTaskManager->getLocalTasks('<front>');
    $this->assertArrayHasKey('tabs', $frontLocalTasks);
    $this->assertIsArray($frontLocalTasks['tabs']);
    $this->assertNotEmpty($frontLocalTasks['tabs']);
    $this->assertArrayHasKey('foo', $frontLocalTasks['tabs']);
    $this->assertTrue($frontLocalTasks['tabs']['foo']);
    $this->assertArrayHasKey('bar', $frontLocalTasks['tabs']);
    $this->assertTrue($frontLocalTasks['tabs']['bar']);

    $this->assertArrayHasKey('cacheability', $frontLocalTasks);
    $cacheability = $frontLocalTasks['cacheability'];
    $this->assertInstanceOf(CacheableMetadata::class, $cacheability);
    $this->assertContains('kittens:dwarf-cat', $cacheability->getCacheTags());
  }

  /**
   * Callback for MenuLocalTasksAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Menu\MenuLocalTasksAlterEvent $event
   *   The event.
   */
  public function onMenuLocalTasksAlter(MenuLocalTasksAlterEvent $event): void {
    if ($event->getRouteName() === '<front>') {
      $data = $event->getData();
      $this->assertArrayHasKey('tabs', $data);
      $this->assertEmpty($data['tabs']);

      $data['tabs'][0]['foo'] = TRUE;
      $event->setData($data);

      $refData = &$event->getData();
      $refData['tabs'][0]['bar'] = TRUE;
    }

    $this->assertNotContains('kittens:dwarf-cat', $event->getCacheability()->getCacheTags());
    $event->getCacheability()->addCacheTags(['kittens:dwarf-cat']);
  }

}
