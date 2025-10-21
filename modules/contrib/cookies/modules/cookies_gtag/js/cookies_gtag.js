/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */;

(function (Drupal) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesGtag = {

    consentGiven: function () {
      var scriptIds = [
        'cookies_gtag_gtag',
        'cookies_gtag_gtag_ajax',
        'cookies_gtag_gtm'
      ];
      for (var scriptId in scriptIds) {
        var script = document.getElementById(scriptIds[scriptId]);
        if (script && script.nodeName === 'SCRIPT') {
          var content = script.innerHTML;
          var newScript = document.createElement('script');
          var attributes = Array.from(script.attributes);
          for (var attr in attributes) {
            var name = attributes[attr].nodeName;
            if (name !== 'type' && name !== 'id') {
              newScript.setAttribute(name, attributes[attr].nodeValue);
            }
          }
          newScript.innerHTML = content;
          script.parentNode.replaceChild(newScript, script);
        }
      }
    },

    attach: function (context) {
      var self = this;
      document.addEventListener('cookiesjsrUserConsent', function (event) {
        var service = (typeof event.detail.services === 'object') ? event.detail.services : {};
        if (typeof service.gtag !== 'undefined' && service.gtag) {
          self.consentGiven(context);
        }
      });
    }
  };
})(Drupal);
