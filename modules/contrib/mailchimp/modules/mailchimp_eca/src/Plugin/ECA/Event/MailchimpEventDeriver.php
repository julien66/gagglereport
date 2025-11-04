<?php

namespace Drupal\mailchimp_eca\Plugin\ECA\Event;

use Drupal\eca\Plugin\ECA\Event\EventDeriverBase;

/**
 * Deriver for Mailchimp ECA event plugins.
 */
class MailchimpEventDeriver extends EventDeriverBase {

  /**
   * {@inheritdoc}
   */
  protected function definitions(): array {
    return MailchimpEvent::definitions();
  }

}
