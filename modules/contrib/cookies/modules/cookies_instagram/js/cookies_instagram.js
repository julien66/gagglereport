/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
(function (Drupal, $) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesInstagram = {

    id: 'instagram',

    consentGiven: function () {
      // Activate scripts in correct order to regard dependencies.
      var i = 0;
      var progress = true;
      while (i < 10 && progress) {
        var id = 'cookies_instagram_' + i;
        var script = document.getElementById(id);
        progress = (script !== null) && (script.nodeName === 'SCRIPT');
        if (progress) {
          // Create new script element to alter the old.
          var src = script.getAttribute('src');
          var newScript = document.createElement('script');
          newScript.setAttribute('id', id);
          if (src) {
            newScript.setAttribute('src', src);
          }
          else {
            newScript.innerHTML = script.innerHTML;
          }
          // Replace script.
          script.parentNode.replaceChild(newScript, script);
        }
        i++;
      }
    },

    consentDenied: function (context) {
      $('.instagram-media', context).cookiesOverlay('instagram');
    },


    attach: function (context) {
      var self = this;
      document.addEventListener('cookiesjsrUserConsent', function (event) {
        var service = (typeof event.detail.services === 'object') ? event.detail.services : {};
        if (typeof service[self.id] !== 'undefined' && service[self.id]) {
          self.consentGiven(context);
        } else {
          self.consentDenied(context);
        }
      });
    }
  };
})(Drupal, jQuery);
