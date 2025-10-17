(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtPurechat = {
    attach: function (context, settings) {
      once('mtPurechatInit', "body", context).forEach(function() {
        window.purechatApi = { l: [], t: [], on: function () { this.l.push(arguments); } }; (function () { var done = false; var script = document.createElement('script'); script.async = true; script.type = 'text/javascript'; script.src = 'https://app.purechat.com/VisitorWidget/WidgetScript'; document.getElementsByTagName('HEAD').item(0).appendChild(script); script.onreadystatechange = script.onload = function (e) { if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) { var w = new PCWidget({c: drupalSettings.basecore.purechatInit.purechatKey, f: true }); done = true; } }; })();
        purechatApi.on('chatbox:ready', function () {
          // values - 'bottomLeft', 'bottomRight', 'topLeft', and 'topRight'
          // Makes the chatbox become docked to bottom-left side of screen
          purechatApi.set('chatbox.position', 'bottomLeft');
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);