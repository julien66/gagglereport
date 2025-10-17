(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtflexsliderMain = {
    attach: function (context, settings) {
      once('mtflexsliderMainInit', '.flexslider-main', context).forEach(function(item) {
        $(item).flexslider({
          animation: drupalSettings.basecore.flexsliderMainInit.slideshowEffect,           // Select your animation type, "fade" or "slide"
          slideshowSpeed: drupalSettings.basecore.flexsliderMainInit.slideshowEffectTime, // Set the speed of the slideshow cycling, in milliseconds
          prevText: "",
          nextText: "",
          pauseOnAction: false,
          useCSS: false
        });
        $(item).fadeIn("slow");
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);