/**
 * @file
 * Contains the behaviors for the "Weighting in action" page.
 */

((once, Drupal) => {
  /**
   * Adds dynamic content to the page.
   *
   * A single JavaScript file can contain multiple behaviors. We place a single
   * behavior per JavaScript file because we show what happens when the weight
   * attribute is set in a .libraries.yml file.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behavior to the #js-example-colors wrapper.
   */
  Drupal.behaviors.jsExampleColorsCmy = {
    attach(context, settings) {
      const [container] = once(
        'js-example-colors-cmy',
        '#js-example-colors',
        context,
      );

      if (container) {
        /** @type {Object.<string, string>} */
        const colors = settings?.jsExample?.colors;

        if (colors) {
          Drupal.jsExample.createColorItem(container, ['cyan'], colors.cyan);
          Drupal.jsExample.createColorItem(
            container,
            ['magenta'],
            colors.magenta,
          );
          Drupal.jsExample.createColorItem(
            container,
            ['yellow'],
            colors.yellow,
          );
        } else {
          Drupal.jsExample.createColorItem(
            container,
            ['error'],
            Drupal.t('No color data for CMY colors.'),
          );
        }
      }
    },
  };
})(once, Drupal);
