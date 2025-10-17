<?php

namespace Drupal\Tests\exclude_node_title\Functional;

/**
 * Tests the default admin settings functionality.
 *
 * @group exclude_node_title
 */
class ExcludeNodeTitleExcludeAllTest extends ExcludeNodeTitleTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    // Make sure to complete the normal setup steps first.
    parent::setUp();

    $config = $this->config('exclude_node_title.settings');
    $config->set('content_types', ['article' => 'all']);
    $config->save();

    $this->drupalCreateNode(['type' => 'article', 'title' => 'Testing title for all Article pages']);
    $this->drupalCreateNode(['type' => 'basic', 'title' => 'Testing title for all Basic pages']);
  }

  /**
   * Tests that 'All' feature works when configured for a single content type.
   *
   * Using default remove render type.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testExcludeNodeTitleAllRemoval(): void {
    // Since no view mode was selected title should still appear.
    $this->drupalGet('node/1');
    $this->assertSession()->pageTextContains('Testing title for all Article pages');

    $this->drupalGet('node/2');
    $this->assertSession()->pageTextContains('Testing title for all Basic pages');

    // Set view module to exclude.
    $config = $this->config('exclude_node_title.settings');
    $config->set('content_type_modes.article', ['full'])->save();

    drupal_flush_all_caches();

    // With a view module configured now it should be hidden.
    $this->drupalGet('node/1');
    $this->assertSession()->pageTextNotContains('Testing title for all Article pages');

    $this->drupalGet('node/2');
    $this->assertSession()->pageTextContains('Testing title for all Basic pages');
  }

  /**
   * Tests that 'All' feature works when configured for a single content type.
   *
   * Using hidden render type.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testExcludeNodeTitleAllHidden(): void {
    $config = $this->config('exclude_node_title.settings');
    $config->set('type', 'hidden')->save();

    // Since no view mode was selected title should still appear.
    $this->drupalGet('node/1');
    $this->assertSession()->pageTextContains('Testing title for all Article pages');
    $this->assertHiddenTitleBlock();

    $this->drupalGet('node/2');
    $this->assertSession()->pageTextContains('Testing title for all Basic pages');
    $this->assertHiddenTitleBlock();

    // Set view module to exclude.
    $config = $this->config('exclude_node_title.settings');
    $config->set('content_type_modes.article', ['full'])->save();

    drupal_flush_all_caches();

    // With a view module configured now it should be hidden but still exist.
    $this->drupalGet('node/1');
    $this->assertSession()->pageTextContains('Testing title for all Article pages');
    $this->assertHiddenTitleBlock(TRUE);

    $this->drupalGet('node/2');
    $this->assertSession()->pageTextContains('Testing title for all Basic pages');
    $this->assertHiddenTitleBlock();
  }

}
