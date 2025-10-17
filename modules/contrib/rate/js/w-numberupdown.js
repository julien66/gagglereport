/**
 * @file
 * Modifies the Rate numberupdown rating.
 */

(function ($, Drupal) {
  Drupal.behaviors.NumberUpDownRating = {
    attach(context, settings) {
      $('body')
        .find('.numberupdown-rating-wrapper')
        .each(function () {
          // If element is editable, enable submit click.
          const isEdit = $(this).attr('can-edit');
          if (isEdit === 'true') {
            $(this)
              .find('label')
              .click(function (e) {
                $(this).find('input').prop('checked', true);
                $(this)
                  .closest('form')
                  .find('.numberupdown-rating-submit')
                  .trigger('click');
              });
          } else {
            this.querySelectorAll('label').forEach((label) => {
              label.style.cursor = 'default'; // Cursor to arrow.
            });
          }
        });
    },
  };
})(jQuery, Drupal);
