<?php

namespace Drupal\mailchimp_events\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Mailchimp event entities.
 */
class MailchimpEventViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
