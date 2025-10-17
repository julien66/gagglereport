(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtowlCarouselAlbums = {
    attach: function (context, settings) {
      once('mtowlCarouselAlbumsInit', ".mt-carousel-albums", context).forEach(function(item) {
        $(item).owlCarousel({
          items: 1,
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
          autoplay: drupalSettings.basecore.owlCarouselAlbumsInit.owlAlbumsAutoPlay,
          autoplayTimeout: drupalSettings.basecore.owlCarouselAlbumsInit.owlAlbumsEffectTime,
          nav: true,
          dots: false,
          loop: true,
          navText: false
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
