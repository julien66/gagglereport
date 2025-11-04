<?php

declare(strict_types=1);

namespace Drupal\Tests\rules\Functional;

use Behat\Mink\Element\NodeElement;
use Drupal\Tests\BrowserTestBase;

/**
 * Has some additional helper methods to make test code more readable.
 */
abstract class RulesBrowserTestBase extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Finds link with specified locator.
   *
   * @param string $locator
   *   Link id, title, text or image alt.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The link node element.
   */
  public function findLink(string $locator): ?NodeElement {
    return $this->getSession()->getPage()->findLink($locator);
  }

  /**
   * Clicks a link identified via partial href using xpath.
   *
   * As the Rules UI pages become more complex, with multiple links and buttons
   * containing the same text, it may get difficult to use clickLink('text', N)
   * where N is the index position on the page, as the index of a given link
   * varies depending on other rules. It is clearer to read and more
   * future-proof to find the link via a known url fragment.
   *
   * @param string $href
   *   The href, or a unique part of it.
   */
  public function clickLinkByHref(string $href): void {
    $this->getSession()->getPage()->find('xpath', './/a[contains(@href, "' . $href . '")]')->click();
  }

  /**
   * Finds field (input, textarea, select) with specified locator.
   *
   * @param string $locator
   *   Input id, name or label.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The input field element.
   */
  public function findField(string $locator): ?NodeElement {
    return $this->getSession()->getPage()->findField($locator);
  }

  /**
   * Finds button with specified locator.
   *
   * @param string $locator
   *   Button id, value or alt.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The button node element.
   */
  public function findButton(string $locator): ?NodeElement {
    return $this->getSession()->getPage()->findButton($locator);
  }

  /**
   * Presses button with specified locator.
   *
   * @param string $locator
   *   Button id, value or alt.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function pressButton(string $locator): void {
    $this->getSession()->getPage()->pressButton($locator);
  }

  /**
   * Fills in field (input, textarea, select) with specified locator.
   *
   * @param string $locator
   *   Input id, name or label.
   * @param string $value
   *   Value.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   *
   * @see \Behat\Mink\Element\NodeElement::setValue
   */
  public function fillField(string $locator, string $value): void {
    $this->getSession()->getPage()->fillField($locator, $value);
  }

  /**
   * Checks or unchecks a checkbox with specified locator.
   *
   * @param string $locator
   *   Input id, name or label.
   * @param bool $value
   *   TRUE is on, FALSE is off.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   *
   * @see \Behat\Mink\Element\NodeElement::setValue
   */
  public function checkField(string $locator, bool $value): void {
    if ($value) {
      $this->getSession()->getPage()->checkField($locator);
    }
    else {
      $this->getSession()->getPage()->uncheckField($locator);
    }
  }

}
