<?php

namespace Drupal\mailchimp_eca\Plugin\ECA\Event;


use Drupal\eca\Attribute\EcaEvent;
use Drupal\eca\Event\Tag;
use Drupal\eca\Plugin\ECA\Event\EventBase;
use Drupal\Component\EventDispatcher\Event;
use Drupal\mailchimp\Event\Authenticate;

/**
 * Plugin implementation of the ECA Events for config.
 */
#[EcaEvent(
  id: 'mailchimp',
  deriver: 'Drupal\mailchimp_eca\Plugin\ECA\Event\MailchimpEventDeriver',
  version_introduced: '1.0.0',
)]
class MailchimpEvent extends EventBase {

  /**
   * {@inheritdoc}
   */
  public static function definitions(): array {
    return [
      'authenticate' => [
        'label' => 'Authenticate',
        'event_name' => Authenticate::AUTHENTICATE,
        'event_class' => Event::class,
        'tags' => Tag::WRITE | Tag::PERSISTENT | Tag::AFTER,
      ],
    ];
  }

}
