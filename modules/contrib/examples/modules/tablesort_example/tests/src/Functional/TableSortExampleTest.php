<?php

namespace Drupal\Tests\tablesort_example\Functional;

use Drupal\Tests\examples\Functional\ExamplesBrowserTestBase;
use Drupal\Core\Url;

/**
 * Verify the tablesort functionality.
 *
 * @group tablesort_example
 * @group examples
 *
 * @ingroup tablesort_example
 */
class TableSortExampleTest extends ExamplesBrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['tablesort_example', 'toolbar'];

  /**
   * The installation profile to use with this test.
   *
   * We need the 'minimal' profile in order to make sure the Tool block is
   * available.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * Verify the functionality of the sortable table.
   */
  public function testTableSortExampleBasic() {
    $assert = $this->assertSession();

    // No need to log in for this test.
    $this->drupalGet('/examples/tablesort-example', ['query' => ['sort' => 'desc', 'order' => 'Numbers']]);
    $assert->statusCodeEquals(200);
    // Ordered by Number, descending.
    $item = $this->getSession()->getPage()->find('xpath', '//tbody/tr/td[1]');
    $this->assertEquals(7, $item->getText(), 'Ordered by Number, descending.');

    $this->drupalGet('/examples/tablesort-example', ['query' => ['sort' => 'asc', 'order' => 'Numbers']]);
    $assert->statusCodeEquals(200);
    // Ordered by Number, ascending.
    $item = $this->getSession()->getPage()->find('xpath', '//tbody/tr/td[1]');
    $this->assertEquals(1, $item->getText(), 'Ordered by Number, ascending.');

    // Sort by Letters.
    $this->drupalGet('/examples/tablesort-example', ['query' => ['sort' => 'desc', 'order' => 'Letters']]);
    $assert->statusCodeEquals(200);
    // Ordered by Letters, descending.
    $item = $this->getSession()->getPage()->find('xpath', '//tbody/tr/td[2]');
    $this->assertEquals('w', $item->getText(), 'Ordered by Letters, descending.');

    $this->drupalGet('/examples/tablesort-example', ['query' => ['sort' => 'asc', 'order' => 'Letters']]);
    $assert->statusCodeEquals(200);
    // Ordered by Letters, ascending.
    $item = $this->getSession()->getPage()->find('xpath', '//tbody/tr/td[2]');
    $this->assertEquals('a', $item->getText(), 'Ordered by Letters, ascending.');

    // Sort by Mixture.
    $this->drupalGet('/examples/tablesort-example', ['query' => ['sort' => 'desc', 'order' => 'Mixture']]);
    $assert->statusCodeEquals(200);
    // Ordered by Mixture, descending.
    $item = $this->getSession()->getPage()->find('xpath', '//tbody/tr/td[3]');
    // cspell:disable-next-line
    $this->assertEquals('t982hkv', $item->getText(), 'Ordered by Mixture, descending.');

    $this->drupalGet('/examples/tablesort-example', ['query' => ['sort' => 'asc', 'order' => 'Mixture']]);
    $assert->statusCodeEquals(200);
    // Ordered by Mixture, ascending.
    $item = $this->getSession()->getPage()->find('xpath', '//tbody/tr/td[3]');
    // cspell:disable-next-line
    $this->assertEquals('0kuykuh', $item->getText(), 'Ordered by Mixture, ascending.');

  }

  /**
   * Verify and validate that default menu links were loaded for this module.
   */
  public function testTableSortExampleLink() {
    $assert = $this->assertSession();
    // Create a user with the permissions we need in order to display the
    // toolbar.
    $this->drupalLogin($this->createUser([
      'access content',
      'access toolbar',
    ]));
    // Our module's routes.
    $links = [
      '' => Url::fromRoute('tablesort_example.description'),
    ];
    // Go to the page and check that the link exists.
    foreach ($links as $page => $url) {
      $this->drupalGet($page);
      $assert->linkByHrefExists($url->getInternalPath());
    }
    // Visit the link and make sure we get a 200 back.
    foreach ($links as $url) {
      $this->drupalGet($url);
      $assert->statusCodeEquals(200);
    }
  }

}
