/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */

(function (Drupal, drupalSettings) {
  'use strict';

  document.cookiesjsr = drupalSettings.cookies.cookiesjsr;

  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesjsr = {
    attach: function() {
      document.addEventListener('cookiesjsrCallbackResponse', function (event) {
        var response = (typeof event.detail.response === 'object') ? event.detail.response : {};
        const messages = new Drupal.Message();
        messages.clear();
        for (var module in response) {
          var obj = response[module];
          for (var i = 0, arr = ['status', 'warning', 'error']; i < arr.length; i++) {
            var type = arr[i];
            if (typeof obj[type] !== 'undefined') {
              messages.add(obj[type], {type: type});
            }
          }
        }
      });
    }
  }
})(Drupal, drupalSettings);
