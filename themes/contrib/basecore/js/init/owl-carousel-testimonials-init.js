(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtowlCarouselTestimonials = {
    attach: function (context, settings) {
      once('mtowlCarouselTestimonialsInit', ".mt-carousel-testimonials", context).forEach(function(item) {
        $(item).owlCarousel({
          items: 1,
          autoplay: drupalSettings.basecore.owlCarouselTestimonialsInit.owlTestimonialsAutoPlay,
          autoplayTimeout: drupalSettings.basecore.owlCarouselTestimonialsInit.owlTestimonialsEffectTime,
          nav: true,
          dots: true,
          loop: false,
          navText: false
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
