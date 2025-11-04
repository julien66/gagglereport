<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Theme;

use Drupal\core_event_dispatcher\Event\Theme\TemplatePreprocessDefaultVariablesAlterEvent;
use Drupal\core_event_dispatcher\ThemeHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Test description.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Theme\TemplatePreprocessDefaultVariablesAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class TemplatePreprocessDefaultVariablesAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * TemplatePreprocessDefaultVariablesAlterEvent test.
   *
   * @throws \Exception
   */
  public function testTemplatePreprocessDefaultVariablesAlterEvent(): void {
    $this->listen(
      ThemeHookEvents::TEMPLATE_PREPROCESS_DEFAULT_VARIABLES_ALTER,
      'onTemplatePreprocessDefaultVariablesAlter'
    );

    $variables = _template_preprocess_default_variables();
    $this->assertArrayHasKey('test_variable', $variables);
    $this->assertTrue($variables['test_variable']);
  }

  /**
   * Callback for TemplatePreprocessDefaultVariablesAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Theme\TemplatePreprocessDefaultVariablesAlterEvent $event
   *   The event.
   */
  public function onTemplatePreprocessDefaultVariablesAlter(TemplatePreprocessDefaultVariablesAlterEvent $event): void {
    $variables = &$event->getVariables();
    $variables['test_variable'] = TRUE;
  }

}
