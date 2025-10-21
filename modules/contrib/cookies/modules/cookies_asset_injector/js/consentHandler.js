/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
(function (Drupal) {
  'use strict';

  /**
   * Define defaults.
   */
  Drupal.behaviors.consentHandler = {
    consentGiven: function (context, settings, serviceId) {
      var blockedAssetsObj = drupalSettings.cookies.cookies_asset_injector.blocked_assets;
      var consentedBlockedAssets = blockedAssetsObj[serviceId];
      if (consentedBlockedAssets) {
        // This service has been unblocked. Unblock asset_injector scripts:
        for (var i in consentedBlockedAssets) {
          if (Object.prototype.hasOwnProperty.call(consentedBlockedAssets, i) && consentedBlockedAssets[i]) {
            var blockedAsset = consentedBlockedAssets[i];
            var scriptId = blockedAsset.script_dom_id;
            var script = document.getElementById(scriptId);
            if (script && script.nodeName === 'SCRIPT') {
              var content = script.innerHTML;
              var newScript = document.createElement('script');
              var attributes = Array.from(script.attributes);
              for (var attr in attributes) {
                if (Object.prototype.hasOwnProperty.call(attributes, attr) && attributes[attr]) {
                  var name = attributes[attr].nodeName;
                  if (name !== 'type' && name !== 'id') {
                    newScript.setAttribute(name, attributes[attr].nodeValue);
                  }
                }
              }
              newScript.innerHTML = content;
              script.parentNode.replaceChild(newScript, script);
            }
          }
        }
      }
    },

    consentDenied: function (context, settings, serviceId) {
      // Nothing to do here.
    },

    attach: function (context, settings) {
      var self = this;
      document.addEventListener('cookiesjsrUserConsent', function (event) {
        var services = (typeof event.detail.services === 'object') ? event.detail.services : {};
        for (var serviceId in services) {
          if (Object.prototype.hasOwnProperty.call(services, serviceId)) {
            var consentGiven = services[serviceId];
            if (consentGiven) {
              self.consentGiven(context, settings, serviceId);
            }
            else {
              self.consentDenied(context, settings, serviceId);
            }
          }
        }
      });
    }
  };
})(Drupal, drupalSettings);
