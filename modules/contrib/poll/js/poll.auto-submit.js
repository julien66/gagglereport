/**
 * @file
 * Poll auto submit.
 */

(($, Drupal, once) => {
  Drupal.behaviors.pollAutoSubmit = {
    attach(context) {
      once('auto-submit-js', '.poll-auto-submit .form-radio', context).forEach(
        (elem) => {
          elem.addEventListener('click', () => {
            $(elem).parents('form').find('.form-submit').trigger('mousedown');
          });
        },
      );
    },
  };
})(jQuery, Drupal, once);
