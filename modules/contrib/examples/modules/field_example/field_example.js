/**
 * @file
 * Javascript for Field Example.
 */

/**
 * Provides a color picker for the fancier widget.
 */
(function ($) {
  Drupal.behaviors.field_example_color_picker = {
    attach() {
      $('.edit-field-example-color-picker').on('focus', function (event) {
        const editField = this;
        const picker = $(this)
          .closest('div')
          .parent()
          .find('.field-example-color-picker');
        // Hide all color pickers except this one.
        $('.field-example-color-picker').hide();
        $(picker).show();
        $.farbtastic(picker, function (color) {
          editField.value = color;
        }).setColor(editField.value);
      });
    },
  };
})(jQuery);
