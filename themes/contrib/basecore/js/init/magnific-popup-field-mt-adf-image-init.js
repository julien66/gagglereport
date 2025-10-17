(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtMagnificPopupAdditionalFeatures = {
    attach: function (context, settings) {
      const element = once('mtMagnificPopupAdditionalFeaturesInit', ".field--name-field-mt-adf-image a.image-popup", context);
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
