(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtowlCarouselProducts = {
    attach: function (context, settings) {
      once('mtowlCarouselProductsInit', ".mt-carousel-products", context).forEach(function(item) {
        $(item).owlCarousel({
          items: 2,
          responsive:{
            0:{
              items:2,
            },
            480:{
              items:2,
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
          autoplay: drupalSettings.basecore.owlCarouselProductsInit.owlProductsAutoPlay,
          autoplayTimeout: drupalSettings.basecore.owlCarouselProductsInit.owlProductsEffectTime,
          nav: true,
          dots: false,
          loop: true,
          navText: false
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
