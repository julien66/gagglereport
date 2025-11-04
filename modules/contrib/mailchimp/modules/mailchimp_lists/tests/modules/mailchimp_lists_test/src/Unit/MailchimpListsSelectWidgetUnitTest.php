<?php

namespace Drupal\Tests\mailchimp_lists_test\Unit;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\mailchimp_lists\Plugin\Field\FieldWidget\MailchimpListsSelectWidget;
use Drupal\Tests\UnitTestCase;

/**
 * Ensure that the correct form values are .
 *
 * @group mailchimp
 */
class MailchimpListsSelectWidgetUnitTest extends UnitTestCase {
  /**
   * The MailchimpListsSelect widget.
   *
   * @var \Drupal\mailchimp_lists\Plugin\Field\FieldWidget\MailchimpListsSelectWidget
   */
  protected $mailchimpListsSelectWidget;

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();
    $field_definition = $this->prophesize(FieldDefinitionInterface::class);
    $this->mailchimpListsSelectWidget = new MailchimpListsSelectWidget('mailchimp_lists_select', [], $field_definition->reveal(), [], []);
  }

  /**
   * @covers Drupal\mailchimp_lists\Plugin\Field\FieldWidget\MailchimpListsSelectWidget::massageFormValues;
   */
  public function testMassageFormValues() {
    // Tests the case where the widget is not attached to an entity form.
    // Example: attaching to a checkout pane.
    $form_state = $this->createMock(FormStateInterface::class);
    $return_value = $this->mailchimpListsSelectWidget->massageFormValues([], [], $form_state);
    $this->assertEquals([], $return_value);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    unset($this->mailchimpListsSelectWidget);
  }

}
