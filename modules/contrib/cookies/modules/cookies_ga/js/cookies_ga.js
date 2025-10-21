/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
(function (Drupal) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesGa = {

    consentGiven: function () {
      var scriptIds = [
        'cookies_ga_ga',
        'cookies_ga_ga_debug',
        'cookies_ga_google_analytics_tracking_script',
        'cookies_ga_google_analytics_tracking_file',
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
        if (typeof service.analytics !== 'undefined' && service.analytics) {
          self.consentGiven(context);
        }
      });
    }
  };
})(Drupal);
