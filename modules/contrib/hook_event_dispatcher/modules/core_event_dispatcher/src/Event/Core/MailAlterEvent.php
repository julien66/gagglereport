<?php

namespace Drupal\core_event_dispatcher\Event\Core;

use Drupal\Component\EventDispatcher\Event;
use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\hook_event_dispatcher\Attribute\HookEvent;
use Drupal\hook_event_dispatcher\Event\EventInterface;

/**
 * Class MailAlterEvent.
 */
#[HookEvent(id: 'mail_alter', alter: 'mail')]
class MailAlterEvent extends Event implements EventInterface {

  /**
   * An array containing the message data.
   *
   * @var array
   */
  protected $message = [];

  /**
   * MailAlterEvent constructor.
   *
   * @param array $message
   *   An array containing the message data.
   */
  public function __construct(array &$message) {
    $this->message = &$message;
  }

  /**
   * Get message data.
   *
   * @return array
   *   The message data.
   */
  public function &getMessage(): array {
    return $this->message;
  }

  /**
   * {@inheritdoc}
   */
  public function getDispatcherType(): string {
    return CoreHookEvents::MAIL_ALTER;
  }

}
