(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtMagnificPopupFieldBnfImage = {
    attach: function (context, settings) {
      const element = once('mtMagnificPopupFieldBnfImageInit', ".field--name-field-mt-bnf-image a.image-popup", context);
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
