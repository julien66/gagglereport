<?php

namespace Drupal\Tests\mailchimp\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\mailchimp\Autoload;
use Drupal\mailchimp_test\MailchimpConfigOverrider;

/**
 * Provides a base class for Mailchimp kernel tests.
 */
abstract class MailchimpKernelTestBase extends KernelTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'mailchimp',
    'mailchimp_test',
  ];

  /**
   * If Mailchimp config should be overridden.
   *
   * @var bool
   */
  protected static $override = TRUE;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Install config.
    $this->installConfig(['mailchimp']);

    // Make sure that an API key is set and that test mode is enabled.
    if ($this::$override) {
      \Drupal::configFactory()->addOverride(new MailchimpConfigOverrider());
    }

    // Register autoloader for loading test classes.
    Autoload::register();

    // Override mailchimp.client_factory service to allow tests to override
    // classes from the mailchimp library.
    $this->container->getDefinition('mailchimp.client_factory')
      ->setClass('Drupal\Tests\mailchimp\Kernel\TestClientFactory');
  }

}
