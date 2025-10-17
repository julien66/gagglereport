(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtSliderRevolutionInternalBannerCarousel = {
    attach: function (context, settings) {
      once('mtSliderRevolutionInternalBannerCarouselInit', ".carousel-internal .rev_slider", context).forEach(function(item) {
        var settings = {
          sliderType:"carousel",
          sliderLayout:"auto",
          gridwidth: [1170,970,750,450],
          gridheight:drupalSettings.basecore.sliderRevolutionInternalBannerCarouselInit.carouselInternalBannerInitialHeight,
          disableProgressBar:"on",
          navigation: {
            onHoverStop:"off",
            arrows:{
              enable:true,
              tmp: "<div class='tp-title-wrap'><span class='tp-arr-titleholder'>{{title}}</span></div>",
              left:{
                h_align:"left",
                v_align:"center",
                h_offset:0,
                v_offset:0
              },
              right:{
                h_align:"right",
                v_align:"center",
                h_offset:0,
                v_offset:0
              }
            },
            bullets:{
              style:"",
              enable: true,
              direction:"horizontal",
              space: 20,
              h_align: drupalSettings.basecore.sliderRevolutionInternalBannerCarouselInit.carouselInternalBannerBulletsPosition,
              v_align:"bottom",
              h_offset: 0,
              v_offset: 30,
              tmp:"<span></span>",
              hide_onleave: false,
              hide_onmobile: false
            },
          },
          carousel: {
            padding_top: 0,
            padding_bottom: 0,
            maxRotation: 0,
            vary_rotation: "off",
            minScale: 60,
            vary_scale: "on",
            horizontal_align: "center",
            vertical_align: "center",
            fadeout: "on",
            vary_fade: "on",
            maxVisibleItems: 7,
            infinity: "on",
            space: -120,
            stretch: "off"
          },
          touch:{
            touchenabled: drupalSettings.basecore.sliderRevolutionInternalBannerCarouselInit.carouselInternalBannerTouchSwipe,
            swipe_treshold:75,
            swipe_min_touches:1,
            drag_block_vertical:false,
            swipe_direction:"horizontal"
          },
          stopLoop:"on",
          stopAfterLoops:0,
          stopAtSlide:1,
          autoHeight:"off"
        }
        $(item).show().revolution(settings);
      });

      $(context).find('.transparent-background').css("backgroundColor", "rgba(0,0,0," + drupalSettings.basecore.slideshowBackgroundOpacity + ")");
      $(context).find('.tp-caption--transparent-background .tp-caption__title').css("backgroundColor", "rgba(0,0,0," + drupalSettings.basecore.slideshowCaptionOpacity + ")");
      $(context).find('.tp-caption--transparent-background .tp-caption__text').css("backgroundColor", "rgba(0,0,0," + drupalSettings.basecore.slideshowCaptionOpacity + ")");

    }
  };
})(jQuery, Drupal, drupalSettings, once);
