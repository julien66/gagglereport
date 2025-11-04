<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\CountriesAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class CountriesAlterEventTest.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\CountriesAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class CountriesAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Tests the CountriesAlterEvent.
   *
   * @throws \Exception
   */
  public function testCountriesAlterEvent(): void {
    $this->listen(CoreHookEvents::COUNTRIES_ALTER, 'onCountriesAlter');

    $countries = $this->container->get('country_manager')->getList();
    $this->assertArrayHasKey('EB', $countries);
    $this->assertEquals('Elbonia', $countries['EB']);
  }

  /**
   * Callback for CountriesAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\CountriesAlterEvent $event
   *   The event.
   */
  public function onCountriesAlter(CountriesAlterEvent $event): void {
    $countries = &$event->getCountries();

    // Elbonia is now independent, so add it to the country list.
    $countries['EB'] = 'Elbonia';
  }

}
