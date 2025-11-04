<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Functional;

use Drupal\node\Entity\NodeType;

/**
 * Tests that the Reaction Rules list builder pages work.
 *
 * @group RulesUi
 */
class UiPageTest extends RulesBrowserTestBase {

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
    $this->storage = $this->container->get('entity_type.manager')->getStorage('rules_reaction_rule');
    $this->adminUser = $this->drupalCreateUser(['administer rules']);
  }

  /**
   * Tests that the reaction rule listing page is reachable.
   */
  public function testReactionRulePage(): void {
    $account = $this->drupalCreateUser(['administer rules']);
    $this->drupalLogin($account);

    $this->drupalGet('admin/config/workflow/rules');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->statusCodeEquals(200);

    // Test that there is an empty reaction rule listing.
    $assert->pageTextContains('There are no enabled reaction rules.');
  }

  /**
   * Tests that creating a reaction rule works.
   */
  public function testCreateReactionRule(): void {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLink('Add reaction rule');

    $this->fillField('Label', 'Test rule');
    $this->fillField('Machine-readable name', 'test_rule');
    $this->fillField('Description', 'This is a test description for a test reaction rule.');
    $this->fillField('React on event', 'rules_entity_insert:node');
    $this->pressButton('Save');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->statusCodeEquals(200);
    $assert->pageTextContains('Reaction rule Test rule has been created.');

    $this->clickLink('Add condition');

    $this->fillField('Condition', 'rules_node_is_promoted');
    $this->pressButton('Continue');

    $this->fillField('context_definitions[node][setting]', 'node');
    $this->pressButton('Save');

    $assert->statusCodeEquals(200);
    $assert->pageTextContains('You have unsaved changes.');

    $this->pressButton('Save');
    $assert->pageTextContains('Reaction rule Test rule has been updated. ');
  }

  /**
   * Tests that enabling and disabling a rule works.
   */
  public function testRuleStatusOperations(): void {
    // Setup an active rule.
    $this->testCreateReactionRule();
    $this->drupalGet('admin/config/workflow/rules');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Test disabling.
    $this->clickLink('Disable');
    $assert->pageTextContains('The reaction rule Test rule has been disabled.');

    // Test enabling.
    $this->clickLink('Enable');
    $assert->pageTextContains('The reaction rule Test rule has been enabled.');
  }

  /**
   * Tests that an event can be added.
   */
  public function testAddEvent(): void {
    // Setup an active rule.
    $this->testCreateReactionRule();

    // Go to "Add event" page.
    $this->clickLink('Add event');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->pageTextContains('Add event to Test rule');
    $assert->pageTextContains('Event selection');
    $assert->pageTextContains('React on event');

    // Select an event.
    $this->findField('events[0][event_name]')->selectOption('rules_entity_update:node');
    $this->pressButton('Add');

    // Click add again to ignore "Restrict by type".
    $this->pressButton('Add');

    $assert->pageTextContains('Added event After updating a content item entity to Test rule.');

    // Assert that the test rule has two events now.
    $expected = ['rules_entity_insert:node', 'rules_entity_update:node'];
    /** @var \Drupal\rules\Entity\ReactionRuleConfig $rule */
    $rule = $this->storage->load('test_rule');
    $this->assertSame($expected, $rule->getEventNames());
  }

  /**
   * Tests that an event with type restriction can be added.
   */
  public function testAddEventWithRestrictByType(): void {
    // Add a content type called 'article'.
    $node_type = NodeType::create([
      'type' => 'article',
      'name' => 'Article',
    ]);
    $node_type->save();

    // Setup an active rule.
    $this->testCreateReactionRule();

    // Go to "Add event" page.
    $this->clickLink('Add event');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->pageTextContains('Add event to Test rule');
    $assert->pageTextContains('Event selection');
    $assert->pageTextContains('React on event');

    // Select an event.
    $this->findField('events[0][event_name]')->selectOption('rules_entity_update:node');
    $this->pressButton('Add');

    // Select bundle 'article'.
    $this->findField('bundle')->selectOption('article');
    $this->pressButton('Add');

    $assert->pageTextContains('Added event After updating a content item entity of type Article to Test rule.');

    // Assert that the second event on the test rule has the bundle selection.
    $expected = [
      'rules_entity_insert:node',
      'rules_entity_update:node--article',
    ];
    /** @var \Drupal\rules\Entity\ReactionRuleConfig $rule */
    $rule = $this->storage->load('test_rule');
    $this->assertSame($expected, $rule->getEventNames());
  }

  /**
   * Tests that an event can be deleted.
   */
  public function testDeleteEvent(): void {
    // Create a rule with two events.
    $rule = $this->storage->create([
      'id' => 'test_rule',
      'label' => 'Test rule',
      'events' => [
        ['event_name' => 'rules_entity_insert:node'],
        ['event_name' => 'rules_entity_update:node'],
      ],
    ]);
    $rule->save();

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Login and go to the rule edit page.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule');

    // Click delete button for second event.
    $this->clickLinkByHref('event-delete/rules_entity_update');

    // Assert we are on the delete page.
    $assert->pageTextContains('Are you sure you want to delete the event After updating a content item entity from Test rule?');

    // And confirm the delete.
    $this->pressButton('Delete');
    $assert->pageTextContains('Deleted event After updating a content item entity from Test rule.');

    // We need to reload the container because the container can get rebuilt
    // when saving a rule.
    $this->resetAll();
    $this->storage = $this->container->get('entity_type.manager')->getStorage('rules_reaction_rule');

    /** @var \Drupal\rules\Entity\ReactionRuleConfig $rule */
    $rule = $this->storage->loadUnchanged('test_rule');

    // Assert that the event is really deleted.
    $this->assertSame(['rules_entity_insert:node'], $rule->getEventNames());
  }

  /**
   * Tests that events cannot be deleted when there is only one event.
   */
  public function testNoDeleteEventWhenRulesHasSingleEvent(): void {
    // Create a rule.
    $rule = $this->storage->create([
      'id' => 'test_rule',
      'label' => 'Test rule',
      'events' => [
        ['event_name' => 'rules_entity_insert:node'],
      ],
    ]);
    $rule->save();

    // Login and go to the rule edit page.
    $this->drupalLogin($this->adminUser);
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule');

    // Assert that no link is displayed for deleting the only event that there
    // is.
    $this->assertNull($this->getSession()->getPage()->find('xpath', './/a[contains(@href, "event-delete/rules_entity_insert")]'));

    // Try to delete the event anyway and assert that access to that page is
    // denied.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule/event-delete/rules_entity_insert:node');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->statusCodeEquals(403);
  }

  /**
   * Tests that cancelling an expression from a rule works.
   */
  public function testCancelExpressionInRule(): void {
    // Setup a rule with one condition.
    $this->testCreateReactionRule();

    $this->clickLink('Add condition');
    $this->fillField('Condition', 'rules_node_is_promoted');
    $this->pressButton('Continue');

    $this->fillField('context_definitions[node][setting]', 'node');
    $this->pressButton('Save');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    $assert->pageTextContains('You have unsaved changes.');

    // Edit and cancel.
    $this->pressButton('Cancel');
    $assert->pageTextContains('Canceled.');

    // Make sure that we are back at the overview listing page.
    $this->assertEquals(1, preg_match('#/admin/config/workflow/rules$#', $this->getSession()->getCurrentUrl()));
  }

  /**
   * Tests that deleting an expression from a rule works.
   */
  public function testDeleteExpressionInRule(): void {
    // Setup a rule with one condition.
    $this->testCreateReactionRule();

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    $this->clickLink('Delete');
    $assert->pageTextContains('Are you sure you want to delete Node is promoted from Test rule?');

    $this->pressButton('Delete');
    $assert->pageTextContains('You have unsaved changes.');

    $this->pressButton('Save');
    $assert->pageTextContains('Reaction rule Test rule has been updated. ');
  }

  /**
   * Tests that a condition with no context can be configured.
   */
  public function testNoContextCondition(): void {
    // Setup a rule with one condition.
    $this->testCreateReactionRule();

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
    $this->testCreateReactionRule();

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();
    // Check that the label shows up on the Rule edit page.
    $assert->pageTextContains('Node is promoted');

    // Edit the condition, negate it, then check the label again.
    $this->clickLink('Edit');
    $this->checkField('Negate', TRUE);
    $this->pressButton('Save');
    $assert->pageTextContains('NOT Node is promoted');
  }

  /**
   * Tests that an action with a 'multiple' context can be configured.
   */
  public function testMultipleContextAction(): void {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLink('Add reaction rule');

    $this->fillField('Label', 'Test rule');
    $this->fillField('Machine-readable name', 'test_rule');
    $this->fillField('React on event', 'rules_entity_insert:node');
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
