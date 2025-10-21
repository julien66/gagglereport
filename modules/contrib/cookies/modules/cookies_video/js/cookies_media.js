/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
 (function (Drupal, $) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesVideo = {
    consentGiven: function (context) {
      $('iframe.cookies-video', context).each(function (i, element) {
        var $element = $(element);
        if ($element.attr('src') !== $element.data('src')) {
          // We only allow http / https protocols to be used in the "src"
          // otherwise continue:
          const src = $element.data('src');
          const protocolRegex = /^(https?)/;
          if (src.substring(0, 1) !== '/') {
            const protocol = src.split(':')[0];
            if (!protocolRegex.test(protocol)) {
              // The src is not http nor https. Continue to the next element:
              return;
            }
          }
          $element.attr('src', $element.data('src'));
          element.removeAttribute('data-src');
        }
      });

      // Blazy module handling:
      $('.cookies-video-blazy-oembed', context).each(function (i, element) {
        var $element = $(element);
        // Unblock blazy iframes, which had additional COOKiES blocking.
        // See cookies_video_preprocess_blazy() for details.
        var $iframe = $element.children('iframe.b-lazy');
        if ($iframe.attr('src') !== $iframe.data('cookies-video-blazy-src')) {
          $iframe.attr('src', $iframe.data('cookies-video-blazy-src'));
        }
        // Show  the hidden container:
        if ($element.hasClass('hidden')) {
          $element.removeClass('hidden');
        }
      });
    },

    consentDenied: function (context) {
      $('iframe.cookies-video, .cookies-video-blazy-oembed', context).cookiesOverlay('video');
    },

    attach: function (context) {
      var self = this;
      document.addEventListener('cookiesjsrUserConsent', function(event) {
        var service = (typeof event.detail.services === 'object') ? event.detail.services : {};
        if (typeof service.video !== 'undefined' && service.video) {
          self.consentGiven(context);
        } else {
          self.consentDenied(context);
        }
      });
    }
  };

})(Drupal, jQuery);
