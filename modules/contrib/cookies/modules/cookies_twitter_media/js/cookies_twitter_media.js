/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
(function (Drupal, $) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesTwitter = {
    initTwitter: {},

    consentGiven: function (context) {
      var $twttrMedia = $('.media--type-twitter', context);
      $twttrMedia.data('status', 'active').removeClass('disabled');
      if (typeof Drupal.behaviors.cookiesTwitter.initTwitter.attach === 'function') {
        Drupal.behaviors.cookiesTwitter.initTwitter.attach();
      }
      if ($twttrMedia.length) {
        var newScript = document.createElement('script');
        newScript.setAttribute('src', '//platform.twitter.com/widgets.js');
        document.body.appendChild(newScript);
      }
    },

    consentDenied: function (context) {
      $('blockquote.twitter-tweet', context).cookiesOverlay('twitter');
    },

    attach: function (context) {
      var self = this;
      if (Drupal.behaviors.hasOwnProperty('twitterMediaEntity')) {
        // Take over the init function and remove it from the original context.
        if (typeof Drupal.behaviors.twitterMediaEntity.attach === 'function') {
          self.initTwitter['attach'] = Drupal.behaviors.twitterMediaEntity.attach;
          self.initTwitter.attach.bind(self.initTwitter);
          Drupal.behaviors.twitterMediaEntity.attach = null;
        }
      }

      document.addEventListener('cookiesjsrUserConsent', function(event) {
        var service = (typeof event.detail.services === 'object') ? event.detail.services : {};
        if (typeof service.twitter !== 'undefined' && service.twitter) {
          self.consentGiven(context);
        } else {
          self.consentDenied(context);
        }
      });
    }
  };
})(Drupal, jQuery);
