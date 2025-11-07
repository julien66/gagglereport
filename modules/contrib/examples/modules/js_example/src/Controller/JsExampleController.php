<?php

namespace Drupal\js_example\Controller;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\examples\Utility\DescriptionTemplateTrait;

/**
 * Controller for JavaScript Example description page.
 *
 * This class uses the DescriptionTemplateTrait to display the module
 * description we put in the templates/description.html.twig file.
 */
class JsExampleController {

  use DescriptionTemplateTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected function getModuleName() {
    return 'js_example';
  }

  /**
   * Weight demonstration.
   *
   * We demonstrate how to attach a number of scripts to the render array via
   * a library.
   *
   * In this controller, on the Drupal side:
   * - We create a container with an ID the scripts uses to identify the
   *   container.
   * - We attach some scripts which generate color-coded content. We use the
   *   'weight' attribute to set the order in which the scripts are included in
   *   the library declaration.
   * - We add values to drupalSettings, which is used to pass data from PHP to
   *   JavaScript.
   *
   * The order in which the color scripts are executed will end up being the order
   * of the content.
   *
   * The 'weight' attribute in the .libraries.yml file determines the order in
   * which a script is added to the page. To see this in action:
   * - Uncheck the "Aggregate Javascript files" setting on
   *   admin/config/development/performance.
   * - Visit examples/js_example/colors and examine the page source.
   *   You will see that the color scripts have been added to <head> following
   *   the weight order.
   *
   * @return array
   *   A render array.
   */
  public function showColors() {
    $colors = [
      'red' => $this->t('I am red.'),
      'blue' => $this->t('I am blue.'),
      'green' => $this->t('I am green.'),
      'cyan' => $this->t('I am cyan.'),
      'magenta' => $this->t('I am magenta.'),
      'yellow' => $this->t('I am yellow.'),
    ];

    // We give use an ID so that to target the HTML markup we added.
    $build['content'] = [
      '#markup' => '<div id="js-example-colors"></div>',
    ];

    // Attach the library.
    $build['#attached']['library'][] = 'js_example/colors';

    // Attach the JavaScript settings.
    $build['#attached']['drupalSettings']['javaScriptExample']['colors'] = $colors;

    return $build;
  }

  /**
   * Accordion page implementation.
   *
   * We're allowing a twig template to define our content in this case,
   * which isn't normally how things work, but it's easier to demonstrate
   * the JavaScript this way.
   *
   * @return array
   *   A render array.
   */
  public function showAccordion() {
    // We get all the page content from a theme hook. This is not a good
    // practice, though: Theme hooks should just theme the content they obtain.
    $build['accordion'] = [
      '#theme' => 'js_example_accordion',
      '#title' => $this->t('Click on sections to expand or collapse them.'),
    ];

    // The usual way to attach a library to a page is adding it to the render
    // array via the #attached property. In this case, we added it via the
    // template file we use. This is why the following lines are commented out.
    //
    // $build['accordion']['#attached']['library'][] = 'js_example/accordion';

    return $build;
  }

}
