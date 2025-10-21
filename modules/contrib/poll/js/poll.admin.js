/**
 * @file
 * Poll admin.
 */

(($, Drupal, once) => {
  $('.poll-existing-choice').on('focus', () => {
    $(once('poll-existing-choice'), document).each(() => {
      $(Drupal.theme('pollChoiceDeleteWarning'))
        .insertBefore($('#choice-values'))
        .hide()
        .fadeIn('slow');
    });
  });

  $.extend(
    Drupal.theme,
    /** @lends Drupal.theme */ {
      /**
       * @return {string}
       *   Markup for the warning.
       */
      pollChoiceDeleteWarning() {
        return `<div class="messages messages--warning" role="alert">${Drupal.t(
          '* Deleting a choice will delete the votes on it!',
        )}</div>`;
      },
    },
  );
})(jQuery, Drupal, once);
