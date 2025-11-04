<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Functional;

use Drupal\rules\Context\ContextConfig;
use Drupal\user\Entity\User;

/**
 * Tests that a rule can be configured and triggered when a node is edited.
 *
 * @group RulesUi
 */
class ConfigureAndExecuteTest extends RulesBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['rules'];

  /**
   * We use the minimal profile because we want to test local action links.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * A user with administration permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $account;

  /**
   * The entity storage for Rules config entities.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * The Rules expression manager.
   *
   * @var \Drupal\rules\Engine\ExpressionManagerInterface
   */
  protected $expressionManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->storage = $this->container->get('entity_type.manager')->getStorage('rules_reaction_rule');
    $this->expressionManager = $this->container->get('plugin.manager.rules_expression');

    // Create an article content type that we will use for testing.
    $type = $this->container->get('entity_type.manager')->getStorage('node_type')
      ->create([
        'type' => 'article',
        'name' => 'Article',
      ]);
    $type->save();

    // Create the user with all needed permissions.
    $this->account = $this->drupalCreateUser([
      'create article content',
      'edit any article content',
      'administer rules',
      'administer site configuration',
    ]);
    $this->drupalLogin($this->account);

    // Create a named role for use in conditions and actions.
    $this->createRole(['administer nodes'], 'test-editor', 'Test Editor');
  }

  /**
   * Helper function to create a reaction rule.
   *
   * @param string $label
   *   The label for the new rule.
   * @param string $machine_name
   *   The internal machine-readable name.
   * @param string $event
   *   The name of the event to react on.
   * @param string $description
   *   Optional description for the reaction rule.
   *
   * @return ReactionRule
   *   The rule object created.
   */
  protected function createRule($label, $machine_name, $event, $description = '') {
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLink('Add reaction rule');
    $this->fillField('Label', $label);
    $this->fillField('Machine-readable name', $machine_name);
    $this->fillField('React on event', $event);
    $this->fillField('Description', $description);
    $this->pressButton('Save');
    $this->assertSession()->pageTextContains('Reaction rule ' . $label . ' has been created');
    $config_factory = $this->container->get('config.factory');
    $rule = $config_factory->get('rules.reaction.' . $machine_name);
    return $rule;
  }

  /**
   * Tests creation of a rule and then triggering its execution.
   */
  public function testConfigureAndExecute(): void {
    // Set up a rule that will show a system message if the title of a node
    // matches "Test title".
    $this->createRule('Test rule', 'test_rule', 'rules_entity_presave:node');

    $this->clickLink('Add condition');
    $this->fillField('Condition', 'rules_data_comparison');
    $this->pressButton('Continue');

    $this->fillField('context_definitions[data][setting]', 'node.title.0.value');
    $this->fillField('context_definitions[value][setting]', 'Test title');
    $this->pressButton('Save');

    $this->clickLink('Add action');
    $this->fillField('Action', 'rules_system_message');
    $this->pressButton('Continue');

    $this->fillField('context_definitions[message][setting]', 'Title matched "Test title"!');
    $this->fillField('context_definitions[type][setting]', 'status');
    $this->pressButton('Save');

    // One more save to permanently store the rule.
    $this->pressButton('Save');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Add a node now and check if our rule triggers.
    $this->drupalGet('node/add/article');
    $this->fillField('Title', 'Test title');
    $this->pressButton('Save');
    $assert->pageTextContains('Title matched "Test title"!');

    // Add a second node with the same title and check the rule triggers again.
    // This tests that the cache update (or non-update) works OK.
    // @see https://www.drupal.org/project/rules/issues/3108494
    $this->drupalGet('node/add/article');
    $this->fillField('Title', 'Test title');
    $this->pressButton('Save');
    $assert->pageTextContains('Title matched "Test title"!');

    // Disable rule and make sure it doesn't get triggered.
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLink('Disable');

    $this->drupalGet('node/add/article');
    $this->fillField('Title', 'Test title');
    $this->pressButton('Save');
    $assert->pageTextNotContains('Title matched "Test title"!');

    // Re-enable the rule and make sure it gets triggered again.
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLink('Enable');

    $this->drupalGet('node/add/article');
    $this->fillField('Title', 'Test title');
    $this->pressButton('Save');
    $assert->pageTextContains('Title matched "Test title"!');

    // Edit the rule and negate the condition.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule');
    $this->clickLink('Edit', 0);
    $this->getSession()->getPage()->checkField('negate');
    $this->pressButton('Save');
    // One more save to permanently store the rule.
    $this->pressButton('Save');

    // Create node with same title and check that the message is not shown.
    $this->drupalGet('node/add/article');
    $this->fillField('Title', 'Test title');
    $this->pressButton('Save');
    $assert->pageTextNotContains('Title matched "Test title"!');
  }

  /**
   * Tests adding an event and then triggering its execution.
   */
  public function testAddEventAndExecute(): void {
    // Create an article.
    $node = $this->drupalCreateNode([
      'type' => 'article',
    ]);

    // Create a rule with a single event and with an action.
    $message = 'Rule is triggered';
    $rule = $this->expressionManager->createRule();
    $rule->addAction('rules_system_message',
        ContextConfig::create()
          ->setValue('message', $message)
          ->setValue('type', 'status')
    );
    $config_entity = $this->storage->create([
      'id' => 'test_rule',
      'label' => 'Test rule',
      'events' => [
        ['event_name' => 'rules_entity_insert:node--article'],
      ],
      'expression' => $rule->getConfiguration(),
    ]);
    $config_entity->save();

    $this->drupalLogin($this->account);

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Now add an event using the UI.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule');

    // Go to "Add event" page.
    $this->clickLink('Add event');
    $assert->pageTextContains('Add event to Test rule');
    $assert->pageTextContains('Event selection');
    $assert->pageTextContains('React on event');

    // Select an event.
    $this->findField('events[0][event_name]')->selectOption('rules_entity_update:node');
    $this->pressButton('Add');

    // Select bundle 'article'.
    $this->findField('bundle')->selectOption('article');
    $this->pressButton('Add');

    // Update an article and assert that the event is triggered.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextContains($message);
  }

  /**
   * Tests deleting an event and then triggering its execution.
   */
  public function testDeleteEventAndExecute(): void {
    // Create a rule with two events and an action.
    $message = 'Rule is triggered';
    $rule = $this->expressionManager->createRule();
    $rule->addAction('rules_system_message',
        ContextConfig::create()
          ->setValue('message', $message)
          ->setValue('type', 'status')
    );
    $config_entity = $this->storage->create([
      'id' => 'test_rule',
      'label' => 'Test rule',
      'events' => [
        ['event_name' => 'rules_entity_insert:node'],
        ['event_name' => 'rules_entity_update:node'],
      ],
      'expression' => $rule->getConfiguration(),
    ]);
    $config_entity->save();

    $this->drupalLogin($this->account);

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Create a node to ensure that the rule is triggered and the message is
    // displayed when creating a node (the first of the two events).
    $this->drupalGet('node/add/article');
    $this->submitForm(['title[0][value]' => 'Foo'], 'Save');
    $assert->pageTextContains($message);

    // Delete an event using the UI.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule');
    // Click delete button for the first event.
    $this->clickLinkByHref('event-delete/rules_entity_insert');

    // Assert we are on the delete page.
    $assert->pageTextContains('Are you sure you want to delete the event After saving a new content item entity from Test rule?');

    // And confirm the delete.
    $this->pressButton('Delete');
    $assert->pageTextContains('Deleted event After saving a new content item entity from Test rule.');

    // Create a node and assert that the event is not triggered.
    $this->drupalGet('node/add/article');
    $this->submitForm(['title[0][value]' => 'Bar'], 'Save');
    $node = $this->drupalGetNodeByTitle('Bar');
    $assert->pageTextNotContains($message);

    // Update it and assert that the message now does get displayed.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextContains($message);
  }

  /**
   * Tests creating and altering two rules reacting on the same event.
   */
  public function testTwoRulesSameEvent(): void {
    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Create a rule that will show a system message when updating a node whose
    // title contains "Two Rules Same Event".
    $rule1 = $this->expressionManager->createRule();
    // Add the condition to the rule.
    $rule1->addCondition('rules_data_comparison',
        ContextConfig::create()
          ->map('data', 'node.title.value')
          ->setValue('operation', 'contains')
          ->setValue('value', 'Two Rules Same Event')
    );
    // Add the action to the rule.
    $message1 = 'RULE ONE is triggered';
    $rule1->addAction('rules_system_message',
        ContextConfig::create()
          ->setValue('message', $message1)
          ->setValue('type', 'status')
    );
    // Add the event and save the rule configuration.
    $config_entity = $this->storage->create([
      'id' => 'rule1',
      'label' => 'Rule One',
      'events' => [['event_name' => 'rules_entity_presave:node']],
      'expression' => $rule1->getConfiguration(),
    ]);
    $config_entity->save();

    // Add a node and check that rule 1 is triggered.
    $this->drupalGet('node/add/article');
    $this->submitForm(['title[0][value]' => 'Two Rules Same Event'], 'Save');
    $node = $this->drupalGetNodeByTitle('Two Rules Same Event');
    $assert->pageTextContains($message1);

    // Repeat to create a second similar rule.
    $rule2 = $this->expressionManager->createRule();
    // Add the condition to the rule.
    $rule2->addCondition('rules_data_comparison',
        ContextConfig::create()
          ->map('data', 'node.title.value')
          ->setValue('operation', 'contains')
          ->setValue('value', 'Two Rules Same Event')
    );
    // Add the action to the rule.
    $message2 = 'RULE TWO is triggered';
    $rule2->addAction('rules_system_message',
        ContextConfig::create()
          ->setValue('message', $message2)
          ->setValue('type', 'status')
    );
    // Add the event and save the rule configuration.
    $config_entity = $this->storage->create([
      'id' => 'rule2',
      'label' => 'Rule Two',
      'events' => [['event_name' => 'rules_entity_presave:node']],
      'expression' => $rule2->getConfiguration(),
    ]);
    $config_entity->save();

    // Edit the node and check that both rules are triggered.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextContains($message1);
    $assert->pageTextContains($message2);

    // Disable rule 2.
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLinkByHref('disable/rule2');

    // Edit the node and check that only rule 1 is triggered.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextContains($message1);
    $assert->pageTextNotContains($message2);

    // Re-enable rule 2.
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLinkByHref('enable/rule2');

    // Check that both rules are triggered.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextContains($message1);
    $assert->pageTextContains($message2);

    // Edit rule 1 and change the message text in the action.
    $message1updated = 'RULE ONE has a new message.';
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/rule1');
    $this->clickLink('Edit', 1);
    $this->fillField('context_definitions[message][setting]', $message1updated);
    // Save the action then save the rule.
    $this->pressButton('Save');
    $this->pressButton('Save');

    // Check that rule 1 now shows the updated text message.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextNotContains($message1);
    $assert->pageTextContains($message1updated);
    $assert->pageTextContains($message2);

    // Delete rule 1.
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLinkByHref('delete/rule1');
    $this->pressButton('Delete');

    // Check that only Rule 2's message is shown.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextNotContains($message1);
    $assert->pageTextNotContains($message1updated);
    $assert->pageTextContains($message2);

    // Disable rule 2.
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLinkByHref('disable/rule2');

    // Check that neither rule's message is shown.
    $this->drupalGet('node/' . $node->id() . '/edit/');
    $this->submitForm([], 'Save');
    $assert->pageTextNotContains($message1);
    $assert->pageTextNotContains($message1updated);
    $assert->pageTextNotContains($message2);
  }

  /**
   * Tests user input in context form for 'multiple' valued context variables.
   */
  public function testMultipleInputContext(): void {
    // Set up a rule. The event is not relevant, we just want a rule to use.
    // Calling $rule = $this->createRule('Test Multiple Input via UI',
    // 'test_rule', 'rules_entity_insert:node') works locally but fails
    // $this->assertEquals($expected_config_value, $to) on drupal.org with
    // 'null does not match expected type "array".', hence revert to the
    // long-hand way of creating the rule.
    $this->drupalGet('admin/config/workflow/rules');
    $this->clickLink('Add reaction rule');
    $this->fillField('Label', 'Test Multiple Input via UI');
    $this->fillField('Machine-readable name', 'test_rule');
    $this->fillField('React on event', 'rules_entity_insert:node');
    $this->pressButton('Save');

    // Add action rules_send_email because the 'to' field has 'multiple = TRUE'
    // rendered as a textarea that we can use for this test.
    $this->clickLink('Add action');
    $this->fillField('Action', 'rules_send_email');
    $this->pressButton('Continue');

    $suboptimal_user_input = [
      "  \r\nwhitespace at beginning of input\r\n",
      "text\r\n",
      "trailing space  \r\n",
      "\rleading terminator\r\n",
      "  leading space\r\n",
      "multiple words, followed by primitive values\r\n",
      "0\r\n",
      "0.0\r\n",
      "128\r\n",
      " false\r\n",
      "true \r\n",
      "null\r\n",
      "terminator r\r",
      "two empty lines\n\r\n\r",
      "terminator n\n",
      "terminator nr\n\r",
      "whitespace at end of input\r\n        \r\n",
    ];
    $this->fillField('context_definitions[to][setting]', implode($suboptimal_user_input));

    // Set the other required fields. These play no part in the test.
    $this->fillField('context_definitions[subject][setting]', 'Hello');
    $this->fillField('context_definitions[message][setting]', 'Dear Heart');

    $this->pressButton('Save');

    // One more save to permanently store the rule.
    $this->pressButton('Save');

    // Now examine the config to ensure the user input was parsed properly
    // and that blank lines, leading and trailing whitespace, and wrong line
    // terminators were removed.
    $expected_config_value = [
      "whitespace at beginning of input",
      "text",
      "trailing space",
      "leading terminator",
      "leading space",
      "multiple words, followed by primitive values",
      "0",
      "0.0",
      "128",
      "false",
      "true",
      "null",
      "terminator r",
      "two empty lines",
      "terminator n",
      "terminator nr",
      "whitespace at end of input",
    ];
    // Need to get the $rule again, as the existing $rule does not have the
    // changes added above and $rule->get('expression.actions...) is empty.
    // @todo Is there a way to refresh $rule and not have to get it again?
    $config_factory = $this->container->get('config.factory');
    $config_factory->clearStaticCache();
    $rule = $config_factory->get('rules.reaction.test_rule');

    $to = $rule->get('expression.actions.actions.0.context_values.to');
    $this->assertEquals($expected_config_value, $to);
  }

  /**
   * Tests the implementation of assignment restriction in context form.
   */
  public function testAssignmentRestriction(): void {
    // Create a rule.
    $rule = $this->expressionManager->createRule();

    // Add a condition which is restricted to selector for 'data', restricted to
    // input for 'operation' but unrestricted on 'value'.
    $condition1 = $this->expressionManager->createCondition('rules_data_comparison');
    $rule->addExpressionObject($condition1);

    // Add an action which is unrestricted on 'message' and 'type' but is
    // restricted to input for 'repeat'.
    $action1 = $this->expressionManager->createAction('rules_system_message');
    $rule->addExpressionObject($action1);

    // As the ContextFormTrait is action/condition agnostic it is not necessary
    // to check an action restricted by selector because the condition covers
    // this. Save the rule to config. No event needed.
    $config_entity = $this->storage->create([
      'id' => 'test_rule',
      'expression' => $rule->getConfiguration(),
    ]);
    $config_entity->save();

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Display the rule edit page to show the actions and conditions.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule');
    $assert->statusCodeEquals(200);

    // Edit the condition and assert that the page loads correctly.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule/edit/' . $condition1->getUuid());
    $assert->statusCodeEquals(200);
    // Check that a switch button is not shown for 'data' and that the field is
    // an autocomplete selector field not plain text entry.
    $assert->buttonNotExists('edit-context-definitions-data-switch-button');
    $assert->elementExists('xpath', '//input[@id="edit-context-definitions-data-setting" and contains(@class, "rules-autocomplete")]');
    // Check that a switch button is not shown for 'operation'.
    $assert->buttonNotExists('edit-context-definitions-operation-switch-button');
    // Check that a switch button is shown for 'value' and that the default
    // field is plain text entry not an autocomplete selector field.
    $assert->buttonExists('edit-context-definitions-value-switch-button');
    $assert->elementExists('xpath', '//input[@id="edit-context-definitions-value-setting" and not(contains(@class, "rules-autocomplete"))]');

    // Edit the action and assert that page loads correctly.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/test_rule/edit/' . $action1->getUuid());
    $assert->statusCodeEquals(200);
    // Check that a switch button is shown for 'message' and that the field is a
    // plain text entry field not an autocomplete selector field.
    $assert->buttonExists('edit-context-definitions-message-switch-button');
    $assert->elementExists('xpath', '//input[@id="edit-context-definitions-message-setting" and not(contains(@class, "rules-autocomplete"))]');
    // Check that a switch button is shown for 'type'.
    $assert->buttonExists('edit-context-definitions-type-switch-button');
    // Check that a switch button is not shown for 'repeat'.
    $assert->buttonNotExists('edit-context-definitions-repeat-switch-button');
  }

  /**
   * Tests upcasting in a condition.
   */
  public function testUpcastInCondition(): void {

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Create a rule.
    $rule = $this->expressionManager->createRule();
    // Add a condition to check if the user has the 'test-editor' role.
    $rule->addCondition('rules_user_has_role',
      ContextConfig::create()
        ->map('user', '@user.current_user_context:current_user')
        ->setValue('roles', ['test-editor'])
    );
    // Add an action to display a system message.
    $message = '-- RULE to test upcasting in condition --';
    $rule->addAction('rules_system_message',
      ContextConfig::create()
        ->setValue('message', $message)
        ->setValue('type', 'status')
    );
    // Set the even to User Login and save the configuration.
    $expr_id = 'test_upcast';
    $config_entity = $this->storage->create([
      'id' => $expr_id,
      'expression' => $rule->getConfiguration(),
      'events' => [['event_name' => 'rules_user_login']],
    ]);
    $config_entity->save();

    // Log in and check that the rule is triggered.
    $this->drupalLogin($this->account);
    $assert->pageTextNotContains($message);

    // Add the required role to the user.
    $this->account->addRole('test-editor');
    $this->account->save();

    // Log out and in and check that the rule is triggered.
    $this->drupalLogout();
    $this->drupalLogin($this->account);
    $assert->pageTextContains($message);

    // Remove the role from the user.
    $this->account->removeRole('test-editor');
    $this->account->save();

    // Log out and in and check that the rule is not triggered.
    $this->drupalLogout();
    $this->drupalLogin($this->account);
    $assert->pageTextNotContains($message);
  }

  /**
   * Tests upcasting in an action.
   */
  public function testUpcastInAction(): void {

    // Log in.
    $this->drupalLogin($this->account);

    // Create a rule.
    $rule = $this->expressionManager->createRule();
    // Add an action to add 'Editor' role to the current user. The role value
    // here is just the machine name as text, and will be upcast to the full
    // role object when the rule is triggered.
    $rule->addAction('rules_user_role_add',
      ContextConfig::create()
        ->map('user', '@user.current_user_context:current_user')
        ->setValue('roles', ['test-editor'])
    );
    // Save the configuration.
    $expr_id = 'test_upcast';
    $config_entity = $this->storage->create([
      'id' => $expr_id,
      'expression' => $rule->getConfiguration(),
      'events' => [['event_name' => 'rules_entity_insert:node']],
    ]);
    $config_entity->save();

    // Check that the user does not have the 'test-editor' role.
    $this->assertEmpty(array_intersect(['test-editor'], $this->account->getRoles()));

    // Create an article, which will trigger the rule, and add the role.
    $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Upcasting role in action',
    ]);

    // Reload the user account.
    $account = User::load($this->account->id());

    // Check that the role has been added to the user.
    $this->assertNotEmpty(array_intersect(['test-editor'], $account->getRoles()));
  }

}
