(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mtTransparentHeader = {
    attach: function (context, settings) {
      // run only if context is document
      if (context !== document) {
        return;
      }

      if ($('.banner .slideshow-fullscreen').length > 0 || $('.banner .main-slideshow-block').length > 0) {
        $('.header-container').addClass('js-transparent-header');
      } else {
        $('.header-container').removeClass('js-transparent-header');
      }
      const header = $('.header-container.js-transparent-header header.header') || null;
      const headerTop = $('.header-container.js-transparent-header .header-top') || null;
      const headerTopHighligted = $('.header-container.js-transparent-header .header-top-highlighted') || null;
      let headerColor = header.css('background-color') || 'rgb(0,0,0)';
      let headerTopColor = headerTop.css('background-color') || 'rgb(0,0,0)';
      let headerTopHighligtedColor = headerTopHighligted.css('background-color') || 'rgb(0,0,0)';

      const rgbToRgba = function (str) {
        return str
          .replace(')', ',' + drupalSettings.basecore.transparentHeader.transparentHeaderOpacity + ')')
          .replace('rgb', 'rgba');
      };

      // if body size is changed observer will get an entry
      const observer = new ResizeObserver(([entry]) => {
        // check if body width is greater than 975px
        if (entry.contentRect.width >= 975) {
          if ($('header.header').length > 0) {
            header.css('background-color', rgbToRgba(headerColor));
          }

          if ($('.header-top').length > 0) {
            headerTop.css('background-color', rgbToRgba(headerTopColor));
          }

          if ($('.header-top-highlighted').length > 0) {
            headerTopHighligted.css('background-color', rgbToRgba(headerTopHighligtedColor));
          }
        } else {
          if ($('header.header').length > 0) {
            header.css('background-color', headerColor);
          }

          if ($('.header-top').length > 0) {
            headerTop.css('background-color', headerTopColor);
          }

          if ($('.header-top-highlighted').length > 0) {
            headerTopHighligted.css('background-color', headerTopHighligtedColor);
          }
        }
      });

      // observe body
      observer.observe(document.body);
    },
  };
})(jQuery, Drupal, drupalSettings);
