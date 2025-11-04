<?php

namespace Drupal\mailchimp_events\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Mailchimp event types.
 *
 * @ingroup mailchimp_events
 */
interface MailchimpEventInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Mailchimp Event name.
   *
   * @return string
   *   Name of the Mailchimp Event.
   */
  public function getName();

  /**
   * Sets the Mailchimp Event name.
   *
   * @param string $name
   *   The Mailchimp Event name.
   *
   * @return \Drupal\mailchimp_events\Entity\MailchimpEventInterface
   *   The called Mailchimp Event entity.
   */
  public function setName($name);

  /**
   * Gets the Mailchimp Event creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Mailchimp Event.
   */
  public function getCreatedTime();

  /**
   * Sets the Mailchimp Event creation timestamp.
   *
   * @param int $timestamp
   *   The Mailchimp Event creation timestamp.
   *
   * @return \Drupal\mailchimp_events\Entity\MailchimpEventInterface
   *   The called Mailchimp Event entity.
   */
  public function setCreatedTime($timestamp);

}
