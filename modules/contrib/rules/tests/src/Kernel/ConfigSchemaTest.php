<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Kernel;

use Drupal\rules\Context\ContextConfig;

/**
 * Tests that action specific config schema works.
 *
 * @group Rules
 */
class ConfigSchemaTest extends RulesKernelTestBase {

  /**
   * The entity storage for Rules config entities.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->storage = $this->container->get('entity_type.manager')->getStorage('rules_component');
  }

  /**
   * Make sure the system send email config schema works on saving.
   */
  public function testMailActionContextSchema(): void {
    // This test does not perform assertions, and the @doesNotPerformAssertions
    // annotation does not work properly in DrupalCI for PHP 7.4.
    // @see https://www.drupal.org/project/rules/issues/3179763
    $this->addToAssertionCount(1);

    $rule = $this->expressionManager
      ->createRule();
    $rule->addAction('rules_send_email', ContextConfig::create()
      ->setValue('to', ['test@example.com'])
      ->setValue('message', 'mail body')
      ->setValue('subject', 'test subject')
    );

    $config_entity = $this->storage->create([
      'id' => 'test_rule',
    ])->setExpression($rule);
    $config_entity->save();
  }

}
