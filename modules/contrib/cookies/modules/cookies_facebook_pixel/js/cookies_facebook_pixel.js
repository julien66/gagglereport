/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
(function (Drupal) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesFbPixel = {

    consentGiven: function () {
      var scriptIds = [
        'cookies_facebook_tracking_pixel_script',
        'facebook_tracking_pixel_script'
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

          // We have to call the attach() from facebook_pixel manually,
          // otherwise the script won't be initialized.
          // TODO: This isn't good but we have a timing issue here, the script might not be loaded yet:
          // @see https://www.drupal.org/project/cookies/issues/3274995#comment-14641051
          setTimeout(function(){
            if (typeof Drupal?.behaviors?.facebook_pixel?.attach === 'function') {
              Drupal.behaviors.facebook_pixel.attach(document);
            }
          }, 2000);
        }
      }
    },

    attach: function (context) {
      var self = this;
      document.addEventListener('cookiesjsrUserConsent', function (event) {
        var service = (typeof event.detail.services === 'object') ? event.detail.services : {};
        if (typeof service.facebook_pixel !== 'undefined' && service.facebook_pixel) {
          self.consentGiven(context);
        }
      });
    }
  };
})(Drupal);
