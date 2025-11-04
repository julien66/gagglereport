<?php

namespace Drupal\Tests\field_event_dispatcher\Kernel;

use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormState;
use Drupal\field_event_dispatcher\Event\Field\AbstractFieldSettingsSummaryFormEvent;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class FieldSettingsSummaryAlterEventTest.
 *
 * @covers \Drupal\field_event_dispatcher\Event\Field\AbstractFieldSettingsSummaryFormEvent
 * @covers \Drupal\field_event_dispatcher\Event\Field\FieldFormatterSettingsSummaryAlterEvent
 * @covers \Drupal\field_event_dispatcher\Event\Field\FieldWidgetSettingsSummaryAlterEvent
 *
 * @group hook_event_dispatcher
 * @group field_event_dispatcher
 *
 * @see \field_event_dispatcher_field_formatter_settings_summary_alter()
 * @see \field_event_dispatcher_field_widget_settings_summary_alter()
 */
class FieldSettingsSummaryAlterEventTest extends KernelTestBase {

  use ListenerTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'entity_test',
    'field',
    'field_ui',
    'text',
    'hook_event_dispatcher',
    'field_event_dispatcher',
  ];

  /**
   * FieldSettingsSummaryAlterEventTest adding summary test.
   *
   * This tests adding an additional summary.
   *
   * @dataProvider addSummaryProvider
   *
   * @throws \Exception
   */
  public function testAddSummary(string $event, string $mode): void {
    $this->listen($event, 'onFieldSettingsSummaryAlter');

    $entityTypeManager = $this->container->get('entity_type.manager');

    $class = $entityTypeManager->getDefinition(sprintf('entity_%s_display', $mode))->getFormClass('edit');
    $formObject = $class::create($this->container);
    $this->assertInstanceOf(EntityFormInterface::class, $formObject);
    $formObject->setModuleHandler($this->container->get('module_handler'));

    $entity = $entityTypeManager->getStorage(sprintf('entity_%s_display', $mode))->create([
      'targetEntityType' => 'entity_test_base_field_display',
      'bundle' => 'entity_test_base_field_display',
    ]);
    $this->assertInstanceOf(EntityDisplayInterface::class, $entity);
    $entity->setComponent('test_display_configurable', [
      'region' => 'content',
    ]);

    $formObject->setEntity($entity);

    $entityTypeManager->getStorage(sprintf('entity_%s_mode', $mode))->create([
      'id' => 'entity_test_base_field_display._custom',
      'targetEntityType' => 'entity_test_base_field_display',
    ])->save();

    $formState = new FormState();
    $form = $formObject->buildForm([], $formState);
    $summary = $form['fields']['test_display_configurable']['settings_summary'];
    $this->assertContains('Test', $summary['#context']['summary']);
  }

  /**
   * Callback for AbstractFieldSettingsSummaryFormEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\AbstractFieldSettingsSummaryFormEvent $event
   *   The event.
   */
  public function onFieldSettingsSummaryAlter(AbstractFieldSettingsSummaryFormEvent $event): void {
    $context = $event->getContext();
    $fieldDefinition = $context['field_definition'];
    $this->assertInstanceOf(FieldDefinitionInterface::class, $fieldDefinition);
    $this->assertEquals('text', $fieldDefinition->getType());

    $summary = &$event->getSummary();
    $summary[] = 'Test';
  }

  /**
   * Data provider for testAddSummary.
   *
   * @return array[]
   *   The provided data.
   */
  public static function addSummaryProvider(): array {
    return [
      'view mode' => [
        FieldHookEvents::FIELD_FORMATTER_SETTINGS_SUMMARY_ALTER,
        'view',
      ],
      'form mode' => [
        FieldHookEvents::FIELD_WIDGET_SETTINGS_SUMMARY_ALTER,
        'form',
      ],
    ];
  }

}
