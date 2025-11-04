<?php

namespace Drupal\Tests\rules\Functional;

/**
 * Tests that each Rules Action can be edited.
 *
 * @todo Add declare(strict_types=1) after figuring out how to handle boolean
 * context value entry. Specifically, in "3. List item add".
 *
 * @group RulesUi
 */
class ActionsFormTest extends RulesBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'ban',
    'path_alias',
    'rules',
    'typed_data',
  ];

  /**
   * We use the minimal profile because we want to test local action links.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * A user account with administration permissions.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Create an article content type that we will use for testing.
    $type = $this->container->get('entity_type.manager')->getStorage('node_type')
      ->create([
        'type' => 'article',
        'name' => 'Article',
      ]);
    $type->save();

    $this->account = $this->drupalCreateUser([
      'administer rules',
      'administer site configuration',
    ]);
    $this->drupalLogin($this->account);

    // Create a named role for use in conditions and actions.
    $this->createRole(['administer nodes'], 'test-editor', 'Test Editor');
  }

  /**
   * Test each action provided by Rules.
   *
   * Check that every action can be added to a rule and that the edit page can
   * be accessed. This ensures that the datatypes used in the definitions do
   * exist. This test does not execute the conditions or actions.
   *
   * @dataProvider dataActionsFormWidgets
   */
  public function testActionsFormWidgets(string $id, array $required = [], array $defaulted = [], array $widgets = [], array $selectors = [], array $provides = []): void {
    $expressionManager = $this->container->get('plugin.manager.rules_expression');
    $storage = $this->container->get('entity_type.manager')->getStorage('rules_reaction_rule');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Create a rule.
    $rule = $expressionManager->createRule();
    // Add the action to the rule.
    $action = $expressionManager->createAction($id);
    $rule->addExpressionObject($action);
    // Save the configuration.
    $expr_id = 'action_' . str_replace(':', '_', $id);
    $config_entity = $storage->create([
      'id' => $expr_id,
      'expression' => $rule->getConfiguration(),
      // Specify a node event so that the node... selector values are available.
      'events' => [['event_name' => 'rules_entity_update:node']],
    ]);
    $config_entity->save();
    // Edit the action and check that the page is generated without error.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/' . $expr_id . '/edit/' . $action->getUuid());
    $assert->statusCodeEquals(200);
    $assert->pageTextContains('Edit ' . $action->getLabel());

    // If any field values have been specified then fill in the form and save.
    if (!empty($required) || !empty($defaulted)) {

      // Switch to data selector if required by the test settings.
      if (!empty($selectors)) {
        foreach ($selectors as $name) {
          $this->pressButton('edit-context-definitions-' . $name . '-switch-button');
          // Check that the switch worked.
          $assert->elementExists('xpath', '//input[@id="edit-context-definitions-' . $name . '-switch-button" and contains(@value, "Switch to the direct input mode")]');
        }
      }

      // Try to save the form before entering the required values.
      if (!empty($required)) {
        $this->pressButton('Save');
        // Check that the form has not been saved.
        $assert->pageTextContains('Error message');
        $assert->pageTextContains('field is required');
        // Fill each required field with the value provided.
        foreach ($required as $name => $value) {
          $this->fillField('edit-context-definitions-' . $name . '-setting', $value);
        }
      }

      // Check that the action can be saved.
      $this->pressButton('Save');
      $assert->pageTextNotContains('InvalidArgumentException: Cannot set a list with a non-array value');
      $assert->pageTextNotContains('Error message');
      $assert->pageTextContains('You have unsaved changes.');
      // Allow for the ?uuid query string being present or absent in the assert
      // method by using addressMatches() with regex instead of addressEquals().
      $assert->addressMatches('#admin/config/workflow/rules/reactions/edit/' . $expr_id . '(\?uuid=' . $action->getUuid() . '|)$#');

      // Check that re-edit and re-save works OK.
      $this->clickLink('Edit');
      if (!empty($defaulted) || !empty($provides)) {
        // Fill each previously defaulted field with the value provided.
        foreach ($defaulted as $name => $value) {
          $this->fillField('edit-context-definitions-' . $name . '-setting', $value);
        }
        foreach ($provides as $name => $value) {
          $this->fillField('edit-provides-' . $name . '-name', $value);
        }
      }

      $this->pressButton('Save');
      $assert->pageTextNotContains('Error message');
      $assert->addressMatches('#admin/config/workflow/rules/reactions/edit/' . $expr_id . '(\?uuid=' . $action->getUuid() . '|)$#');

      // Save the rule.
      $this->pressButton('Save');
      $assert->pageTextContains("Reaction rule $expr_id has been updated");
    }

  }

  /**
   * Data provider - provides data for testActionsFormWidgets().
   *
   * @return array
   *   The test data array. The top level keys are free text but should be short
   *   and relate to the test case. The values are ordered arrays of test case
   *   data with elements that must appear in the following order:
   *   - Machine name of the action being tested.
   *   - (optional) Required values to enter on the Context form. This is an
   *     associative array with keys equal to the field names and values equal
   *     to the required field values.
   *   - (optional) Values for fields that have defaults. This is an associative
   *     array with keys equal to the field names and values equal to the field
   *     values. These are used on the second edit, to alter the fields that
   *     have been saved with their default value.
   *   - (optional) Widget types we expect to see on the Context form. This is
   *     an associative array with keys equal to the field names as above, and
   *     values equal to expected widget type.
   *   - (optional) Names of fields for which the selector/direct input button
   *     needs pressing to 'data selection' before the field value is entered.
   *   - (optional) Provides values. This is an associative array with keys
   *     equal to the field names and values equal to values to set.
   */
  public static function dataActionsFormWidgets(): array {
    // Instead of directly returning the full set of test data, create variable
    // $data to hold it. This allows for manipulation before the final return.
    $data = [
      // Data.
      '1. Data calculate value' => [
        // Machine name.
        'rules_data_calculate_value',
        // Required values.
        [
          'input-1' => '3',
          'operator' => '*',
          'input-2' => '4',
        ],
        // Defaulted values.
        [],
        // Widgets.
        [
          'input-1' => 'text-input',
          'operator' => 'text-input',
          'input-2' => 'text-input',
        ],
        // Selectors.
        [],
        // Provides.
        ['result' => 'new_named_variable'],
      ],
      '2. Data convert' => [
        'rules_data_convert',
        ['value' => 'node.uid', 'target-type' => 'string'],
        ['rounding-behavior' => 'up'],
      ],
      '3. List item add' => [
        'rules_list_item_add',
        [
          'list' => 'node.uid.entity.roles',
          'item' => '1',
        ],
        [
          'unique' => TRUE,
          'position' => 'start',
        ],
      ],
      '4. List item remove' => [
        'rules_list_item_remove',
        ['list' => 'node.uid.entity.roles', 'item' => '1'],
      ],
      '5. Data set - direct' => [
        'rules_data_set',
        ['data' => 'node.title'],
        ['value' => 'abc'],
      ],
      '6. Data set - selector' => [
        'rules_data_set',
        [
          'data' => 'node.title',
          'value' => '@user.current_user_context:current_user.name.value',
        ],
        [],
        [],
        ['value'],
      ],
      '7. Variable add' => [
        'rules_variable_add',
        ['type' => 'integer', 'value' => 'node.nid'],
      ],

      // Entity.
      '8. Entity delete' => [
        'rules_entity_delete',
        ['entity' => 'node'],
      ],
      '9. Entity fetch by field - selector' => [
        // Machine name.
        'rules_entity_fetch_by_field',
        // Required values.
        ['type' => 'node', 'field-name' => 'nid', 'field-value' => 'node.uid'],
        // Defaulted values.
        ['limit' => 5],
        // Widgets.
        [],
        // Selectors.
        ['field-value'],
        // Provides.
        ['entity-fetched' => 'new_named_variable'],
      ],
      '10. Entity fetch by field - direct' => [
        'rules_entity_fetch_by_field',
        ['type' => 'node', 'field-name' => 'sticky', 'field-value' => 1],
      ],
      '11. Entity fetch by id' => [
        'rules_entity_fetch_by_id',
        ['type' => 'node', 'entity-id' => 123],
      ],
      '12. Entity save' => [
        'rules_entity_save',
        ['entity' => 'node'],
        ['immediate' => TRUE],
      ],

      // Content.
      '13. Entity create node' => [
        'rules_entity_create:node',
        ['type' => 'article', 'title' => 'abc'],
      ],
      '14. Node make sticky' => [
        'rules_node_make_sticky',
        ['node' => 'node'],
      ],
      '15. Node make unsticky' => [
        'rules_node_make_unsticky',
        ['node' => 'node'],
      ],
      '16. Node publish' => [
        'rules_node_publish',
        ['node' => 'node'],
      ],
      '17.Node unpublish' => [
        'rules_node_unpublish',
        ['node' => 'node'],
      ],
      '18. Node promote' => [
        'rules_node_promote',
        ['node' => 'node'],
      ],
      '19. Node unpromote' => [
        'rules_node_unpromote',
        ['node' => 'node'],
      ],

      // Path.
      '20. Path alias create' => [
        'rules_path_alias_create',
        ['source' => '/node/1', 'alias' => 'abc'],
        ['language' => 'en'],
      ],
      '21. Entity path alias create' => [
        'rules_entity_path_alias_create:entity:node',
        ['entity' => 'node', 'alias' => 'abc'],
      ],
      '22. Path alias delete by alias' => [
        'rules_path_alias_delete_by_alias',
        ['alias' => 'abc'],
      ],
      '23. Path alias delete by path' => [
        'rules_path_alias_delete_by_path',
        ['path' => '/node/1'],
      ],

      // System.
      '24. Page redirect' => [
        'rules_page_redirect',
        ['url' => '/node/1'],
      ],
      '25. Email to users of role' => [
        'rules_email_to_users_of_role',
        [
          'roles' => 'test-editor',
          'subject' => 'Hello',
          'message' => "Some text\nLine two",
        ],
        ['reply' => 'test@example.com', 'language' => 'und'],
        ['message' => 'textarea'],
      ],
      '26. System message' => [
        'rules_system_message',
        ['message' => 'Some text'],
        ['type' => 'warning', 'repeat' => 0],
      ],
      '27. Send email - direct input' => [
        'rules_send_email',
        [
          'to' => 'test@example.com',
          'subject' => 'Some testing subject',
          'message' => 'Test with direct input of recipients',
        ],
        ['reply' => 'test@example.com', 'language' => 'en'],
        ['message' => 'textarea'],
      ],
      '28. Send email - data selector for address' => [
        'rules_send_email',
        [
          'to' => 'node.uid.entity.mail.value',
          'subject' => 'Some testing subject',
          'message' => 'Test with selector input of node author',
        ],
        ['reply' => 'test@example.com'],
        ['message' => 'textarea'],
        ['to'],
      ],

      // User.
      '29. Entity create user' => [
        'rules_entity_create:user',
        // The name should be required, but can save with blank name.
        // @todo fix this. Then move 'name' into the required array.
        [],
        ['name' => 'fred'],
      ],
      '30. Send account email' => [
        'rules_send_account_email',
        ['user' => 'node.uid', 'email-type' => 'password_reset'],
      ],
      '31. User block' => [
        'rules_user_block',
        ['user' => '@user.current_user_context:current_user'],
        [],
        [],
        ['user'],
      ],
      '32. User role add' => [
        'rules_user_role_add',
        [
          'user' => '@user.current_user_context:current_user',
          'roles' => 'test-editor',
        ],
        [],
        [],
        ['user'],
      ],
      '33. User role remove' => [
        'rules_user_role_remove',
        [
          'user' => '@user.current_user_context:current_user',
          'roles' => 'test-editor',
        ],
      ],
      '34. Unblock user' => [
        'rules_user_unblock',
        ['user' => '@user.current_user_context:current_user'],
        [],
        [],
        ['user'],
      ],

      // Ban.
      '35. Ban IP' => [
        'rules_ban_ip',
        [],
        ['ip' => '192.0.2.1'],
      ],
      '36. Unban IP' => [
        'rules_unban_ip',
        [],
        ['ip' => '192.0.2.1'],
      ],
    ];

    // Selecting the 'to' email address using data selector will not work until
    // single data selector values with multiple = True are converted to arrays.
    // Error "Expected a list data type ... but got a email data type instead".
    // @see https://www.drupal.org/project/rules/issues/2723259
    // @todo Delete this unset() when the above issue is fixed.
    unset($data['28. Send email - data selector for address']);

    // Use unset $data['The key to remove']; to remove a temporarily unwanted
    // item, use return [$data['Key to test'], $data['Another']]; to selectively
    // test some items, or use return $data; to test everything.
    return $data;
  }

}
