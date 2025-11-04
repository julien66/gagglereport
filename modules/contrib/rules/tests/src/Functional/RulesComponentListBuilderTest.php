<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Functional;

/**
 * Tests that the Rules Component list builder pages work.
 *
 * @group RulesUi
 */
class RulesComponentListBuilderTest extends RulesBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['rules', 'rules_test'];

  /**
   * We use the minimal profile because we want to test local action links.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * The entity storage for Rules config entities.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * A user with administration permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    $this->storage = $this->container->get('entity_type.manager')->getStorage('rules_component');
    $this->adminUser = $this->drupalCreateUser(['administer rules']);
  }

  /**
   * Tests that the rule component listing page is reachable.
   */
  public function testRuleComponentPage(): void {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/workflow/rules/components');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->statusCodeEquals(200);

    // Test that there is an empty rules component listing.
    $assert->pageTextContains('No rules components have been defined.');
  }

  /**
   * Tests that creating a rules component works.
   */
  public function testCreateRulesComponent(): void {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/workflow/rules/components');
    $this->clickLink('Add component');

    $this->fillField('Label', 'Test component');
    $this->fillField('Machine-readable name', 'test_component');
    $this->fillField('Description', 'This is a test description for a test component.');
    $this->pressButton('Save');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->statusCodeEquals(200);
    $assert->pageTextContains('Component Test component has been created.');

    $this->clickLink('Add condition');
    $this->fillField('Condition', 'rules_user_is_blocked');
    $this->pressButton('Continue');

    $this->fillField('context_definitions[user][setting]', '@user:current_user_context:current_user');
    $this->pressButton('Save');

    $assert->statusCodeEquals(200);
    $assert->pageTextContains('You have unsaved changes.');

    $this->pressButton('Save');
    $assert->pageTextContains('Rule component Test component has been updated. ');
  }

  /**
   * Tests that cancelling an expression from a component works.
   */
  public function testCancelExpressionInComponent(): void {
    // Setup a rule with one condition.
    $this->testCreateRulesComponent();

    $this->clickLink('Add condition');
    $this->fillField('Condition', 'rules_user_is_blocked');
    $this->pressButton('Continue');

    $this->fillField('context_definitions[user][setting]', '@user:current_user_context:current_user');
    $this->pressButton('Save');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->pageTextContains('You have unsaved changes.');

    // Edit and cancel.
    $this->pressButton('Cancel');
    $assert->pageTextContains('Canceled.');

    // Make sure that we are back at the overview listing page.
    $this->assertEquals(1, preg_match('#/admin/config/workflow/rules/components$#', $this->getSession()->getCurrentUrl()));
  }

  /**
   * Tests that deleting an expression from a rule works.
   */
  public function testDeleteExpressionInComponent(): void {
    // Setup a rule with one condition.
    $this->testCreateRulesComponent();

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    $this->clickLink('Delete');
    $assert->pageTextContains('Are you sure you want to delete User is blocked from Test component?');

    $this->pressButton('Delete');
    $assert->pageTextContains('You have unsaved changes.');

    $this->pressButton('Save');
    $assert->pageTextContains('Rule component Test component has been updated. ');
  }

  /**
   * Tests that a condition with no context can be configured.
   */
  public function testNoContextCondition(): void {
    // Setup a rule with one condition.
    $this->testCreateRulesComponent();

    $this->clickLink('Add condition');
    // The rules_test_true condition does not define context in its annotation.
    $this->fillField('Condition', 'rules_test_true');
    $this->pressButton('Continue');
    // Pressing 'Save' will generate an exception and the test will fail if
    // Rules does not support conditions without a context.
    // Exception: Warning: Invalid argument supplied for foreach().
    $this->pressButton('Save');
  }

  /**
   * Tests that a negated condition has NOT prefixed to its label.
   */
  public function testNegatedCondition(): void {
    // Setup a rule with one condition.
    $this->testCreateRulesComponent();

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    // Check that the label shows up on the Rule edit page.
    $assert->pageTextContains('User is blocked');

    // Edit the condition, negate it, then check the label again.
    $this->clickLink('Edit');
    $this->checkField('Negate', TRUE);
    $this->pressButton('Save');
    $assert->pageTextContains('NOT User is blocked');
  }

  /**
   * Tests that an action with a 'multiple' context can be configured.
   */
  public function testMultipleContextAction(): void {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/workflow/rules/components');
    $this->clickLink('Add component');

    $this->fillField('Label', 'Test component');
    $this->fillField('Machine-readable name', 'test_component');
    $this->fillField('Description', 'This is a test description for a test component.');
    $this->pressButton('Save');

    $this->clickLink('Add action');
    $this->fillField('Action', 'rules_send_email');
    $this->pressButton('Continue');

    // Push the data selection switch 2 times to make sure that also works and
    // does not throw PHP notices.
    $this->pressButton('Switch to data selection');
    $this->pressButton('Switch to the direct input mode');

    $this->fillField('context_definitions[to][setting]', 'klausi@example.com');
    $this->fillField('context_definitions[subject][setting]', 'subject');
    $this->fillField('context_definitions[message][setting]', 'message');
    $this->pressButton('Save');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->statusCodeEquals(200);
  }

}
