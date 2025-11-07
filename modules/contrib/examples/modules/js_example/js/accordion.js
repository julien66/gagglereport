/**
 * @file
 * Contains the accordion behaviors.
 */

((once, Drupal) => {
  /**
   * Hides and shows the accordion items.
   *
   * @type {Drupal~behavior}
   *
   * @prop {Drupal~behaviorAttach} attach
   *   Attaches the behavior to the accordion wrapper.
   */
  Drupal.behaviors.javaScriptExampleAccordion = {
    attach(context) {
      once(
        'javascript-example-accordion',
        '.accordion-wrapper',
        context,
      ).forEach((accordion) => {
        const items = accordion.querySelectorAll('.accordion-item');
        const headers = accordion.querySelectorAll('.accordion-item-header');

        /**
         * Toggles the visibility of accordion items.
         *
         * @param {Event} e
         *   The triggered click event.
         */
        const toggleItem = (e) => {
          /** @type {HTMLDivElement} */
          const clickedItem = e.currentTarget.parentNode;

          for (let i = 0; i < items.length; i++) {
            items[i].classList.add('close');
            items[i].classList.remove('open');
          }

          if (clickedItem.classList.contains('close')) {
            clickedItem.classList.remove('close');
            clickedItem.classList.add('open');
          }
        };

        for (let i = 0; i < headers.length; i++) {
          headers[i].addEventListener('click', toggleItem);
        }
      });
    },
  };
})(once, Drupal);
