<?php

namespace Drupal\Tests\preprocess_event_dispatcher\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\preprocess_event_dispatcher\Event\PreprocessEventInterface;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class PreprocessModuleTest.
 *
 * @group hook_event_dispatcher
 * @group preprocess_event_dispatcher
 *
 * @see \preprocess_event_dispatcher_preprocess()
 */
class PreprocessModuleTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'system',
    'hook_event_dispatcher',
    'preprocess_event_dispatcher',
  ];

  /**
   * Preprocess hook test.
   *
   * @throws \Exception
   */
  public function testPreprocessHook(): void {
    $this->listen('preprocess_page', 'onPreprocess');
    $this->container->get('theme.manager')->render('page', [
      'page' => [
        'test' => TRUE,
      ],
    ]);
  }

  /**
   * Callback for PreprocessEventInterface.
   *
   * @param \Drupal\preprocess_event_dispatcher\Event\PreprocessEventInterface $event
   *   The event.
   */
  public function onPreprocess(PreprocessEventInterface $event): void {
    $this->assertTrue($event->getVariables()->get('test'));
  }

}
