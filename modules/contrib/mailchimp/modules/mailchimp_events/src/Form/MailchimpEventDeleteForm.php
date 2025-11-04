<?php

namespace Drupal\mailchimp_events\Form;

use Drupal\Core\Entity\ContentEntityDeleteForm;

/**
 * Provides a form for deleting Mailchimp event types.
 *
 * @ingroup mailchimp_events
 */
class MailchimpEventDeleteForm extends ContentEntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->t(
      '<p>Deleting an event type will prevent drupal from triggering the same event again,
       but already logged events will still exist in the Mailchimp Activity Feed.</p>
       <p>Be sure to also delete any webform handlers and code that calls on this event entity.</p>'
    );
  }

}
