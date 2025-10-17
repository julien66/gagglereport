/**
 * @file
 * Modifies the Rate fivestar rating.
 */

(function ($, Drupal) {
  Drupal.behaviors.fiveStarRating = {
    attach(context, settings) {
      $('body')
        .find('.fivestar-rating-wrapper')
        .each(function () {
          $(this)
            .find('.fivestar-rating-input:checked')
            .each(function (i) {
              $(this)
                .parents('div')
                .prevAll()
                .addBack()
                .children('label')
                .addClass('full');
            });

          // If element is editable, enable hover and click.
          const isEdit = $(this).attr('can-edit');
          if (isEdit === 'true') {
            $(this)
              .find('label')
              .click(function (e) {
                $(this)
                  .parents('div')
                  .prevAll()
                  .addBack()
                  .children('label')
                  .addClass('full');
                $(this)
                  .parents('div')
                  .nextAll()
                  .children('label')
                  .removeClass('full');
                $(this).find('input').prop('checked', true);
                $(this).closest('form').find('.form-submit').trigger('click');
              })
              .hover(
                function () {
                  this.style.cursor = 'pointer'; // Cursor to pointer.
                  $(this)
                    .parents('div')
                    .prevAll()
                    .addBack()
                    .children('label')
                    .addClass('hover');
                },
                function () {
                  $(this)
                    .parents('div')
                    .prevAll()
                    .addBack()
                    .children('label')
                    .removeClass('hover');
                },
              );
          } else {
            this.querySelectorAll('label').forEach((label) => {
              label.style.cursor = 'default'; // Cursor to arrow.
            });
          }
        });
    },
  };
})(jQuery, Drupal);
