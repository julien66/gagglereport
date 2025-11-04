<?php

namespace Drupal\Tests\field_event_dispatcher\Kernel;

use Drupal\Core\Entity\Display\EntityDisplayInterface;
use Drupal\Core\Entity\EntityDisplayBase;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Form\FormState;
use Drupal\field_event_dispatcher\Event\Field\AbstractFieldThirdPartySettingsFormEvent;
use Drupal\field_event_dispatcher\Event\Field\FieldFormatterThirdPartySettingsFormEvent;
use Drupal\field_event_dispatcher\Event\Field\FieldWidgetThirdPartySettingsFormEvent;
use Drupal\field_event_dispatcher\FieldHookEvents;
use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\hook_event_dispatcher\Kernel\ListenerTrait;

/**
 * Class FieldFormatterThirdPartySettingsFormEventTest.
 *
 * @covers \Drupal\field_event_dispatcher\Event\Field\AbstractFieldThirdPartySettingsFormEvent
 * @covers \Drupal\field_event_dispatcher\Event\Field\FieldFormatterThirdPartySettingsFormEvent
 * @covers \Drupal\field_event_dispatcher\Event\Field\FieldWidgetThirdPartySettingsFormEvent
 *
 * @group hook_event_dispatcher
 * @group field_event_dispatcher
 *
 * @see \field_event_dispatcher_field_formatter_third_party_settings_form()
 * @see \field_event_dispatcher_field_widget_third_party_settings_form()
 */
class FieldThirdPartySettingsFormEventTest extends KernelTestBase {

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
   * FieldThirdPartySettingsFormEvent adding elements test.
   *
   * This tests adding third-party form elements.
   *
   * @dataProvider addingElementsProvider
   *
   * @throws \Exception
   */
  public function testAddingElements(string $event, string $mode, string $method): void {
    $this->listen($event, $method);

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
    $formState->set('plugin_settings_edit', 'test_display_configurable');

    $form = $formObject->buildForm([], $formState);

    $settingsEditForm = $form['fields']['test_display_configurable']['plugin']['settings_edit_form'];
    $this->assertArrayHasKey('third_party_settings', $settingsEditForm);
    $this->assertArrayHasKey('field_event_dispatcher', $settingsEditForm['third_party_settings']);
    $this->assertEquals([
      'test_module' => [
        'test' => [],
      ],
    ], $settingsEditForm['third_party_settings']['field_event_dispatcher']);
  }

  /**
   * Callback for FieldFormatterThirdPartySettingsFormEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\FieldFormatterThirdPartySettingsFormEvent $event
   *   The event.
   */
  public function onFieldFormatterThirdPartySettingsForm(FieldFormatterThirdPartySettingsFormEvent $event): void {
    $this->assertEquals('text_default', $event->getPlugin()->getPluginId());
    $this->assertEquals(EntityDisplayBase::CUSTOM_MODE, $event->getViewMode());

    $this->onFieldThirdPartySettingsForm($event);
  }

  /**
   * Callback for FieldWidgetThirdPartySettingsFormEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\FieldWidgetThirdPartySettingsFormEvent $event
   *   The event.
   */
  public function onFieldWidgetThirdPartySettingsForm(FieldWidgetThirdPartySettingsFormEvent $event): void {
    $this->assertEquals('text_textfield', $event->getPlugin()->getPluginId());
    $this->assertEquals(EntityDisplayBase::CUSTOM_MODE, $event->getFormMode());

    $this->onFieldThirdPartySettingsForm($event);
  }

  /**
   * Assertion for AbstractFieldThirdPartySettingsFormEvent.
   *
   * @param \Drupal\field_event_dispatcher\Event\Field\AbstractFieldThirdPartySettingsFormEvent $event
   *   The event.
   */
  private function onFieldThirdPartySettingsForm(AbstractFieldThirdPartySettingsFormEvent $event): void {
    $this->assertEquals('text', $event->getFieldDefinition()->getType());
    $this->assertEquals('test_display_configurable', $event->getFormState()->get('plugin_settings_edit'));

    $form = $event->getForm();
    $this->assertContains('test_display_configurable', $form['#fields']);

    $event->addElements('test_module', [
      'test' => [],
    ]);
  }

  /**
   * Data provider for testAddingElements.
   *
   * @return array[]
   *   The provided data.
   */
  public static function addingElementsProvider(): array {
    return [
      'view mode' => [
        FieldHookEvents::FIELD_FORMATTER_THIRD_PARTY_SETTINGS_FORM,
        'view',
        'onFieldFormatterThirdPartySettingsForm',
      ],
      'form mode' => [
        FieldHookEvents::FIELD_WIDGET_THIRD_PARTY_SETTINGS_FORM,
        'form',
        'onFieldWidgetThirdPartySettingsForm',
      ],
    ];
  }

}
