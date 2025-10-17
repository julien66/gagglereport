(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtFlexsliderNodesSlideshow = {
    attach: function (context, settings) {

      once('mtFlexsliderNodesSlideshowShow', '.nodes-slideshow', context).forEach(function(item) {
        $(item).fadeIn("slow");
      });

      once('mtFlexsliderNodesSlideshowNavShow', '.nodes-slideshow-navigation', context).forEach(function(item) {
        $(item).fadeIn("slow");
      });

      // The slider being synced must be initialized first
      once('mtFlexsliderNodesSlideshowInit', '.nodes-slideshow', context).forEach(function(item) {
        var blockId = $(item).closest(".block").attr('id'),
        nodesSlideshow = "#" + blockId + " .nodes-slideshow",
        nodesSlideshowThumbs = "#" + blockId + " .nodes-slideshow-navigation";

        $(nodesSlideshowThumbs).flexslider({
          animation: "slide",
          controlNav: false,
          animationLoop: false,
          slideshow: false,
          directionNav: false,
          prevText: "",
          nextText: "",
          asNavFor: nodesSlideshow
        });
        $(nodesSlideshow).flexslider({
          useCSS: false,
          animation: "slide",
          controlNav: false,
          directionNav: false,
          prevText: "",
          nextText: "",
          animationLoop: false,
          slideshow: false,
          sync: nodesSlideshowThumbs
        });
      });

      $(context).find('.nodes-slideshow-navigation .slides').on('touchstart', 'li', function() {
        $(this).addClass("is-active");
        $(this).siblings().removeClass("is-active");
      });

      $(context).find('.nodes-slideshow-navigation .slides').on('click', 'li', function() {
        $(this).addClass("is-active");
        $(this).siblings().removeClass("is-active");
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
