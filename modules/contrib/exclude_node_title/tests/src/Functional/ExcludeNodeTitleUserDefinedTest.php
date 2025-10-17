<?php

namespace Drupal\Tests\exclude_node_title\Functional;

/**
 * Tests the user defined functionality.
 *
 * @group exclude_node_title
 */
class ExcludeNodeTitleUserDefinedTest extends ExcludeNodeTitleTestBase {

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    // Make sure to complete the normal setup steps first.
    parent::setUp();

    $config = $this->config('exclude_node_title.settings');
    $config->set('content_types', ['article' => 'user']);
    $config->save();

    $this->drupalCreateNode(['type' => 'article', 'title' => 'Testing title for all Article pages']);
    $this->drupalCreateNode(['type' => 'basic', 'title' => 'Testing title for all Basic pages']);
  }

  /**
   * Tests user defined functionality.
   *
   * Using default remove render type.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testExcludeNodeTitleUserDefined(): void {
    $this->drupalGet('node/1');
    $this->assertSession()->pageTextContains('Testing title for all Article pages');

    $this->drupalGet('node/2');
    $this->assertSession()->pageTextContains('Testing title for all Basic pages');

    $this->drupalGet('node/add/article');
    $this->assertSession()->pageTextContains('Exclude title from display');
    $this->submitForm([
      'exclude_node_title' => TRUE,
    ], 'Save');
    $this->assertSession()->pageTextNotContains('Testing title for all Article pages');

    $this->drupalGet('node/add/basic');
    $this->assertSession()->pageTextNotContains('Exclude title from display');
  }

}
