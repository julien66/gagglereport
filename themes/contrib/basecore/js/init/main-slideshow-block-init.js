(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtMainSlideshowBlock = {
    attach: function (context, settings) {
      once('mtMainSlideshowBlockInit', ".main-slideshow-block .rev_slider", context).forEach(function(item) {
        $(item).show().revolution({
          sliderType:"standard",
          sliderLayout: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-layout'),
          gridwidth: [1170,970,750,450],
          gridheight: parseInt($(item).closest(".main-slideshow-block").attr('data-attribute-mt-initial-height')),
          autoHeight: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-autoheight'),
          fullScreenOffsetContainer: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-ignore-header'),
          delay: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-effect-duration'),
          disableProgressBar:'off',
          responsiveLevels:[1199,991,767,480],
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
            tabs: {
              style:"",
              enable: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-tabs-enable'),
              width:410,
              height:95,
              min_width:240,
              wrapper_padding: 0,
              wrapper_opacity:"1",
              tmp:'<div class="tp-tab-content"><span class="tp-tab-title">{{title}}</span></div>',
              visibleAmount: 6,
              hide_onmobile: false,
              hide_onleave: false,
              direction:"horizontal",
              span: true,
              position:"outer-bottom",
              space:0,
              h_align:"left",
              v_align:"bottom",
              h_offset:0,
              v_offset:0
            },
            bullets:{
              style:"",
              enable: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-bullets-enable'),
              direction:"horizontal",
              space: 5,
              h_align: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-bullets-position'),
              v_align:"bottom",
              h_offset: 0,
              v_offset: 20,
              tmp:"",
            },
            touch:{
              touchenabled: $(item).closest(".main-slideshow-block").attr('data-attribute-mt-touch-swipe-nav'),
              swipe_treshold:75,
              swipe_min_touches:1,
              drag_block_vertical:false,
              swipe_direction:"horizontal"
            }
          }
        });

        $(item).find(".ms-transparent-background").each(function() {
          var captionOpacity = $(this).attr('data-attribute-mt-caption-opacity'),
           captionBackgroundColor = $(this).find('.tp-caption__title').css("background-color").replace(")", "," + captionOpacity + ")").replace("rgb", "rgba");
           $(this).find('.tp-caption__title').css("background-color", captionBackgroundColor);
           $(this).find('.tp-caption__text').css("background-color", captionBackgroundColor);
        });

        $(item).find(".transparent-background").css("backgroundColor", "rgba(0,0,0," + $(item).closest(".main-slideshow-block").attr('data-attribute-mt-background-opacity') + ")");
      });

    }
  };
})(jQuery, Drupal, drupalSettings, once);
