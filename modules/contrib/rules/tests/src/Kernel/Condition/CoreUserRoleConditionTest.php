<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Kernel\Condition;

use Drupal\Tests\user\Kernel\Condition\UserRoleConditionTest;

/**
 * Tests the user role condition with the Rules condition manager.
 *
 * Rules extends core condition plugins with certain new features that are
 * used within Rules. To ensure the Rules ConditionManager still discovers
 * core condition plugins, and to ensure that these core condition plugins
 * operate as expected by core when using the Rules ConditionManager, we use
 * this test. It is a simple subclass of the core UserRoleConditionTest, so
 * it will always be up-to-date with the core test.
 *
 * @group Rules
 */
class CoreUserRoleConditionTest extends UserRoleConditionTest {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['rules', 'typed_data'];

}
