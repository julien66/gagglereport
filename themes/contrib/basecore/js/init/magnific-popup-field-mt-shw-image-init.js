(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtMagnificPopupFieldShwImage = {
    attach: function (context, settings) {
      const element = once('mtMagnificPopupFieldShwImageInit', ".field--name-field-mt-shw-image a.image-popup", context);
      $(element).magnificPopup({
        type:"image",
        removalDelay: 300,
        mainClass: "mfp-fade",
        gallery: {
          enabled: true, // set to true to enable gallery
        },
        image: {
          titleSrc: function(item) {
            return item.el.closest('.overlay-container').children()[1].title || '';
          }
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
