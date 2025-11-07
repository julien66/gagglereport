/**
 * @file
 * Contains code that is shared between the colors.*.js files.
 */

((Drupal) => {
  /**
   * The namespace for the code added by the JavaScript Example module.
   *
   * @namespace
   */
  Drupal.jsExample = {
    /**
     * Creates a new color element.
     *
     * @param {HTMLElement} container
     *   The parent element.
     * @param {string[]} classes
     *   The CSS class to apply to the new element.
     * @param {string} content
     *   The element content, added with {@link HTMLElement#textContent}.
     */
    createColorItem(container, classes, content) {
      const item = document.createElement('div');
      item.classList.add(...classes);
      item.textContent = content;
      container.appendChild(item);
    },
  };
})(Drupal);
