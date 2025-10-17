(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtowlCarouselFeaturesCarousel = {
    attach: function (context, settings) {
      once('mtowlCarouselFeaturesCarouselInit', ".mt-features-carousel", context).forEach(function(item) {
        $(item).owlCarousel({
          items: 4,
          responsive:{
            0:{
              items:1,
            },
            480:{
              items:1,
            },
            768:{
              items:2,
            },
            992:{
              items:2,
            },
            1200:{
              items:4,
            },
            1680:{
              items:4,
            }
          },
          autoplay: false,
          autoplayTimeout: 5000,
          nav: true,
          dots: false,
          loop: true,
          navText: true
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
