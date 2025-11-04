<?php

namespace Drupal\Tests\mailchimp\Kernel;

use Drupal\mailchimp\ClientFactory;
use Mailchimp\MailchimpApiUser;

/**
 * Provides a mechanic to override classes from the Mailchimp Library.
 */
class TestClientFactory extends ClientFactory {

  /**
   * Sets a namespaced class for a short class name.
   *
   * @param string $classname
   *   Relative class name for a Mailchimp Library object.
   * @param \Mailchimp\MailchimpApiUser $instance
   *   The instantiated class.
   *
   * @return $this
   */
  public function setInstance(string $classname, MailchimpApiUser $instance): TestClientFactory {
    $this->instances[$classname] = $instance;
    return $this;
  }

  /**
   * Unsets a class override.
   *
   * @param string $classname
   *   Relative class name for a Mailchimp Library object.
   *
   * @return $this
   */
  public function unsetInstance($classname): TestClientFactory {
    unset($this->instances[$classname]);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getByClassName(string $classname = 'MailchimpApiUser'): MailchimpApiUser {
    if (isset($this->instances[$classname])) {
      return $this->instances[$classname];
    }
    return parent::getByClassName($classname);
  }

}
