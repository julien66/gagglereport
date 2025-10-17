(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtowlCarouselCollections = {
    attach: function (context, settings) {
      once('mtowlCarouselCollectionsInit', ".mt-carousel-collections", context).forEach(function(item) {
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
          autoplay: drupalSettings.basecore.owlCarouselCollectionsInit.owlCollectionsAutoPlay,
          autoplayTimeout: drupalSettings.basecore.owlCarouselCollectionsInit.owlCollectionsEffectTime,
          nav: true,
          dots: false,
          loop: true,
          navText: false
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
