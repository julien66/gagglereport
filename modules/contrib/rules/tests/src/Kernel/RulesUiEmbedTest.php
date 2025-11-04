<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Kernel;

use Drupal\rules\Ui\RulesUiConfigHandler;
use Drupal\rules\Ui\RulesUiDefinition;

/**
 * Tests embedding the Rules UI.
 *
 * @group RulesUi
 */
class RulesUiEmbedTest extends RulesKernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'rules',
    'rules_test_ui_embed',
    'system',
    'user',
  ];

  /**
   * The rules UI manager.
   *
   * @var \Drupal\rules\Ui\RulesUiManagerInterface
   */
  protected $rulesUiManager;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->rulesUiManager = $this->container->get('plugin.manager.rules_ui');

    $this->installConfig(['system']);
    $this->installConfig(['rules_test_ui_embed']);
  }

  /**
   * @covers \Drupal\rules\Ui\RulesUiManager
   */
  public function testUiManager(): void {
    $definition = $this->rulesUiManager->getDefinitions();
    $this->assertArrayHasKey('rules_test_ui_embed.settings_conditions', $definition);
    $this->assertInstanceOf(RulesUiDefinition::class, $definition['rules_test_ui_embed.settings_conditions']);
    $this->assertNotEmpty($definition['rules_test_ui_embed.settings_conditions']->label);
    $this->assertEquals(RulesUiConfigHandler::class, $definition['rules_test_ui_embed.settings_conditions']->getClass());
  }

}
