<?php

namespace Drupal\Tests\core_event_dispatcher\Kernel\Core;

use Drupal\Core\Validation\Plugin\Validation\Constraint\IsNullConstraint;
use Drupal\core_event_dispatcher\CoreHookEvents;
use Drupal\core_event_dispatcher\Event\Core\ValidationConstraintAlterEvent;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;
use Symfony\Component\Validator\Constraints\IsNull;

/**
 * Class ValidationConstraintAlterEvent.
 *
 * @covers \Drupal\core_event_dispatcher\Event\Core\ValidationConstraintAlterEvent
 *
 * @group hook_event_dispatcher
 * @group core_event_dispatcher
 */
class ValidationConstraintAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'hook_event_dispatcher',
    'core_event_dispatcher',
  ];

  /**
   * Test ValidationConstraintAlterEvent.
   *
   * @throws \Exception
   */
  public function testValidationConstraintAlterEvent(): void {
    $this->listen(CoreHookEvents::VALIDATION_CONSTRAINT_ALTER, 'onValidationConstraintAlter');

    $constraintManager = $this->container->get('validation.constraint');
    $this->assertTrue($constraintManager->hasDefinition('Null'));
    $definition = $constraintManager->getDefinition('Null');
    $this->assertEquals(IsNull::class, $definition['class']);
  }

  /**
   * Callback for ValidationConstraintAlterEvent.
   *
   * @param \Drupal\core_event_dispatcher\Event\Core\ValidationConstraintAlterEvent $event
   *   The event.
   */
  public function onValidationConstraintAlter(ValidationConstraintAlterEvent $event): void {
    $definition = $event->getDefinition('Null');
    $this->assertEquals(IsNullConstraint::class, $definition['class']);
    $definition['class'] = IsNull::class;
    $event->setDefinition('Null', $definition);
  }

}
