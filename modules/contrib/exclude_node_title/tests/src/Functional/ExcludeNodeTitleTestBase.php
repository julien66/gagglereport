<?php

namespace Drupal\Tests\exclude_node_title\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Base class for exclude_node_title tests.
 */
abstract class ExcludeNodeTitleTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $profile = 'minimal';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['exclude_node_title', 'node'];

  /**
   * {@inheritdoc}
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  protected function setUp(): void {
    // Make sure to complete the normal setup steps first.
    parent::setUp();

    $this->drupalCreateContentType(['type' => 'article', 'name' => 'Article']);
    $this->drupalCreateContentType(['type' => 'basic', 'name' => 'Basic']);

    $this->drupalLogin($this->drupalCreateUser([
      'access content',
      'access administration pages',
      'administer site configuration',
      'bypass node access',
      'administer exclude node title',
      'exclude any node title',
    ]));
  }

  /**
   * Re-usable code to assert hidden title block.
   *
   * @param bool $hidden
   *   If the block should be visually-hidden.
   */
  protected function assertHiddenTitleBlock(bool $hidden = FALSE): void {
    $title_block = $this->xpath('//div[@id = "block-stark-page-title"]');
    $this->assertEquals(1, count($title_block));
    $title = $title_block[0]->find('css', 'h1');
    if ($hidden) {
      $this->assertTrue($title->hasClass('visually-hidden'));
    }
    else {
      $this->assertFalse($title->hasClass('visually-hidden'));
    }
  }

}
