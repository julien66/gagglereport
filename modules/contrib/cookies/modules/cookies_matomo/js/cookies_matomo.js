/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
(function (Drupal) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesMatomo = {

    consentGiven: function () {
      var scriptIds = [
        'cookies_matomo'
      ];
      for (var i in scriptIds) {
        var script = document.getElementById(scriptIds[i]);
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
        if (typeof service.matomo !== 'undefined' && service.matomo) {
          self.consentGiven(context);
        }
      });
    }
  };
})(Drupal);
