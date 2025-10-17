(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtMagnificPopupFieldVideo = {
    attach: function (context, settings) {
      const element = once('mtMagnificPopupFieldVideoInit', ".iframe-popup > a", context);
      $(element).magnificPopup({
        disableOn: 700,
        type: "iframe",
        mainClass: "mfp-fade",
        removalDelay: 160,
        preloader: false,
        fixedContentPos: false,
        gallery: {
          enabled: true, // set to true to enable gallery
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
