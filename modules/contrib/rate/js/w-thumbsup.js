/**
 * @file
 * Modifies the Rate thumbsup rating.
 */

(function ($, Drupal) {
  Drupal.behaviors.ThumbsUpRating = {
    attach(context, settings) {
      $('body')
        .find('.thumbsup-rating-wrapper')
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
                  .find('.thumbsup-rating-submit')
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
