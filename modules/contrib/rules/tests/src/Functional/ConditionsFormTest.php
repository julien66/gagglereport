<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Functional;

/**
 * Tests that each Rules Condition can be edited.
 *
 * @group RulesUi
 */
class ConditionsFormTest extends RulesBrowserTestBase {

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
   * Test each condition provided by Rules.
   *
   * Check that every condition can be added to a rule and that the edit page
   * can be accessed. This ensures that the datatypes used in the definitions
   * do exist. This test does not execute the conditions or actions.
   *
   * @dataProvider dataConditionsFormWidgets
   */
  public function testConditionsFormWidgets(string $id, array $required = [], array $defaulted = [], array $widgets = [], array $selectors = []): void {
    $expressionManager = $this->container->get('plugin.manager.rules_expression');
    $storage = $this->container->get('entity_type.manager')->getStorage('rules_reaction_rule');

    /** @var \Drupal\Tests\WebAssert $assert */
    $assert = $this->assertSession();

    // Create a rule.
    $rule = $expressionManager->createRule();
    // Add the condition to the rule.
    $condition = $expressionManager->createCondition($id);
    $rule->addExpressionObject($condition);
    // Save the configuration.
    $expr_id = 'condition_' . str_replace(':', '_', $id);
    $config_entity = $storage->create([
      'id' => $expr_id,
      'expression' => $rule->getConfiguration(),
      // Specify a node event so that the node... selector values are available.
      'events' => [['event_name' => 'rules_entity_update:node']],
    ]);
    $config_entity->save();
    // Edit the condition and check that the page is generated without error.
    $this->drupalGet('admin/config/workflow/rules/reactions/edit/' . $expr_id . '/edit/' . $condition->getUuid());
    $assert->statusCodeEquals(200);
    $assert->pageTextContains('Edit ' . $condition->getLabel());

    // If any field values have been specified then fill in the form and save.
    if (!empty($required) || !empty($defaulted)) {

      // Switch to data selector where required.
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

      // Check that the condition can be saved.
      $this->pressButton('Save');
      $assert->pageTextNotContains('InvalidArgumentException: Cannot set a list with a non-array value');
      $assert->pageTextNotContains('Error message');
      $assert->pageTextContains('You have unsaved changes.');
      // Allow for the ?uuid query string being present or absent in the assert
      // method by using addressMatches() with regex instead of addressEquals().
      $assert->addressMatches('#admin/config/workflow/rules/reactions/edit/' . $expr_id . '(\?uuid=' . $condition->getUuid() . '|)$#');

      // Check that re-edit and re-save works OK.
      $this->clickLink('Edit');
      if (!empty($defaulted)) {
        // Fill each previously defaulted field with the value provided.
        foreach ($defaulted as $name => $value) {
          $this->fillField('edit-context-definitions-' . $name . '-setting', $value);
        }
      }

      $this->pressButton('Save');
      $assert->pageTextNotContains('Error message');
      $assert->addressMatches('#admin/config/workflow/rules/reactions/edit/' . $expr_id . '(\?uuid=' . $condition->getUuid() . '|)$#');

      // Save the rule.
      $this->pressButton('Save');
      $assert->pageTextContains("Reaction rule $expr_id has been updated");
    }
  }

  /**
   * Data provider - provides data for testConditionsFormWidgets().
   *
   * @return array
   *   The test data array. The top level keys are free text but should be short
   *   and relate to the test case. The values are ordered arrays of test case
   *   data with elements that must appear in the following order:
   *   - Machine name of the condition being tested.
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
   */
  public static function dataConditionsFormWidgets(): array {
    // Instead of directly returning the full set of test data, create variable
    // $data to hold it. This allows for manipulation before the final return.
    $data = [

      // Data.
      '1. Data comparison' => [
        // Machine name.
        'rules_data_comparison',
        // Required values.
        [
          'data' => 'node.title.value',
          'value' => 'node_unchanged.title.value',
        ],
        // Defaulted values.
        ['operation' => 'contains'],
        // Widgets.
        [
          'data' => 'text-input',
          'operation' => 'text-input',
          'value' => 'text-input',
        ],
        // Selectors.
        ['value'],
      ],
      '2. Data is empty' => [
        'rules_data_is_empty',
        ['data' => 'node.title.value'],
      ],
      '3. List contains' => [
        'rules_list_contains',
        ['list' => 'node.uid.entity.roles', 'item' => 'abc'],
        [],
        ['list' => 'textarea'],
      ],
      '4. List count is' => [
        'rules_list_count_is',
        [
          'list' => 'node.uid.entity.roles',
          'value' => '2',
        ],
        ['operator' => '<='],
      ],
      '5. Text comparison - direct' => [
        'rules_text_comparison',
        ['text' => 'node.title.value', 'match' => 'abc'],
      ],
      '6. Text comparison - selector' => [
        'rules_text_comparison',
        [
          'text' => 'node.title.value',
          'match' => 'node.uid.entity.name.value',
        ],
        ['operator' => 'ends'],
        [],
        ['match'],
      ],

      // Entity.
      '7. Entity has field' => [
        'rules_entity_has_field',
        ['entity' => 'node', 'field' => 'login'],
      ],
      '8. Entity is new' => [
        'rules_entity_is_new',
        ['entity' => 'node'],
      ],
      '9. Entity is of bundle' => [
        'rules_entity_is_of_bundle',
        ['entity' => 'node', 'type' => 'node', 'bundle' => 'article'],
      ],
      '10. Entity is of type' => [
        'rules_entity_is_of_type',
        ['entity' => 'node', 'type' => 'path_alias'],
      ],

      // Content.
      '11. Node is of type' => [
        'rules_node_is_of_type',
        ['node' => 'node', 'types' => 'article'],
      ],
      '12. Node is promoted' => [
        'rules_node_is_promoted',
        ['node' => 'node'],
      ],
      '13. Node is published' => [
        'rules_node_is_published',
        ['node' => 'node'],
      ],
      '14. Node is sticky' => [
        'rules_node_is_sticky',
        ['node' => 'node'],
      ],

      // Path.
      '15. Path alias exists' => [
        'rules_path_alias_exists',
        ['alias' => '/abc'],
        ['language' => 'und'],
      ],
      '16. Path has alias' => [
        'rules_path_has_alias',
        ['path' => '/node/1'],
        ['language' => 'en'],
      ],

      // User.
      '17. Entity field access' => [
        'rules_entity_field_access',
        [
          'entity' => 'node',
          'field' => 'timezone',
          'user' => '@user.current_user_context:current_user',
        ],
        ['operation' => 'edit'],
      ],
      '18. User has role' => [
        'rules_user_has_role',
        [
          'user' => '@user.current_user_context:current_user',
          'roles' => 'test-editor',
        ],
        ['operation' => 'OR'],
        [],
        ['user'],
      ],
      '19. User is blocked' => [
        'rules_user_is_blocked',
        ['user' => '@user.current_user_context:current_user'],
        [],
        [],
        ['user'],
      ],

      // Ban.
      '20. Ip is banned' => [
        'rules_ip_is_banned',
        [],
        ['ip' => '192.0.2.1'],
      ],
    ];

    // Use unset $data['The key to remove']; to remove a temporarily unwanted
    // item, use return [$data['The key to test']]; to selectively test just one
    // item, or use return $data; to test everything.
    return $data;
  }

}
