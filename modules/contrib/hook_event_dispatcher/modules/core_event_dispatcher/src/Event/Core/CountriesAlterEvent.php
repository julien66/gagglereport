<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class CountriesAlterEvent.
 */
#[HookEvent(id: 'countries_alter', alter: 'countries')]
class CountriesAlterEvent extends Event implements EventInterface {

  /**
   * The associative array of countries keyed by two-letter country code.
   *
   * @var array
   */
  protected $countries = [];

  /**
   * CountriesAlterEvent constructor.
   *
   * @param array $countries
   *   The associative array of countries keyed by two-letter country code.
   */
  public function __construct(array &$countries) {
    $this->countries = &$countries;
  }

  /**
   * Gets the associative array of countries keyed by two-letter country code.
   *
   * @return array
   *   The associative array of countries keyed by two-letter country code.
   */
  public function &getCountries(): array {
    return $this->countries;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::COUNTRIES_ALTER;
  }

}
