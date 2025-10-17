(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtSliderRevolutionFullWidth = {
    attach: function (context, settings) {
      once('mtSliderRevolutionFullWidthInit', ".slideshow-fullwidth .rev_slider", context).forEach(function(item) {
        if (drupalSettings.basecore.sliderRevolutionFullWidthInit.slideshowFullWidthNavigationStyle == "bullets") {
          var bulletsEnable = true,
          tabsEnable = false;
        } else {
          var tabsEnable = true,
          bulletsEnable = false;
        }
        var settings = {
          sliderType: "standard",
          sliderLayout: "auto",
          gridheight: parseInt(drupalSettings.basecore.sliderRevolutionFullWidthInit.slideshowFullWidthInitialHeight),
          delay: drupalSettings.basecore.sliderRevolutionFullWidthInit.slideshowFullWidthEffectTime,
          disableProgressBar: 'off',
          navigation: {
            onHoverStop: "off",
            arrows:{
              enable: true,
              tmp: "<div class='tp-title-wrap'><span class='tp-arr-titleholder'>{{title}}</span></div>",
              left:{
                h_align: "left",
                v_align: "center",
                h_offset: 0,
                v_offset: 0
              },
              right:{
                h_align: "right",
                v_align: "center",
                h_offset: 0,
                v_offset: 0
              }
            },
            tabs: {
              style: "",
              enable: tabsEnable,
              width: 410,
              height: 95,
              min_width: 240,
              wrapper_padding: 0,
              wrapper_opacity: "1",
              tmp: '<div class="tp-tab-content"><span class="tp-tab-title">{{title}}</span></div>',
              visibleAmount: 6,
              hide_onmobile: false,
              hide_onleave: false,
              direction: "horizontal",
              span: true,
              position: "outer-bottom",
              space: 0,
              h_align: "left",
              v_align: "bottom",
              h_offset: 0,
              v_offset: 0
            },
            bullets:{
              style: "",
              enable: bulletsEnable,
              direction: "horizontal",
              space: 5,
              h_align: drupalSettings.basecore.sliderRevolutionFullWidthInit.slideshowFullWidthBulletsPosition,
              v_align: "bottom",
              h_offset: 0,
              v_offset: 20,
              tmp: "",
            },
            touch:{
              touchenabled: drupalSettings.basecore.sliderRevolutionFullWidthInit.slideshowFullWidthTouchSwipe,
              swipe_treshold: 75,
              swipe_min_touches: 1,
              drag_block_vertical: false,
              swipe_direction: "horizontal"
            }
          }
        };
        if ( drupalSettings.basecore.sliderRevolutionFullWidthInit.slideshowFullWidthAutoHeight ) {
          autoHeightSettings = {
            autoHeight: 'on',
          }
          $.extend(settings, autoHeightSettings);
        } else {
          autoHeightSettings = {
            autoHeight: 'off',
            responsiveLevels: [1199,991,767,480],
            gridwidth: [1170,970,750,450],
          };
          $.extend(settings, autoHeightSettings);
        }
        $(item).show().revolution(settings);
      });

      $(context).find('.transparent-background').css("backgroundColor", "rgba(0,0,0," + drupalSettings.basecore.slideshowBackgroundOpacity + ")");
      $(context).find('.tp-caption--transparent-background .tp-caption__title').css("backgroundColor", "rgba(0,0,0," + drupalSettings.basecore.slideshowCaptionOpacity + ")");
      $(context).find('.tp-caption--transparent-background .tp-caption__text').css("backgroundColor", "rgba(0,0,0," + drupalSettings.basecore.slideshowCaptionOpacity + ")");

    }
  };
})(jQuery, Drupal, drupalSettings, once);
