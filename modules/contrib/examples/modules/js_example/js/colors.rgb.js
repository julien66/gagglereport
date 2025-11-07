/**
 * @file
 * Contains the behaviors for the "Weighting in action" page.
 */

((once, Drupal) => {
  /**
   * Adds dynamic content to the page.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behavior to the #js-example-colors wrapper.
   */
  Drupal.behaviors.javaScriptColorsRgb = {
    attach(context, settings) {
      const [container] = once(
        'js-example-colors-rgb',
        '#js-example-colors',
        context,
      );

      if (container) {
        /** @type {Object.<string, string>} */
        const colors = settings?.jsExample?.colors;

        if (colors) {
          Drupal.jsExample.createColorItem(container, ['red'], colors.red);
          Drupal.jsExample.createColorItem(container, ['green'], colors.green);
          Drupal.jsExample.createColorItem(container, ['blue'], colors.blue);
        } else {
          Drupal.jsExample.createColorItem(
            container,
            ['error'],
            Drupal.t('No color data for RGB colors.'),
          );
        }
      }
    },
  };
})(once, Drupal);
