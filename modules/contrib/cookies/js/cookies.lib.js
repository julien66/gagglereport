/**
 * @todo Remove code if IE11 not supported anymore.
 * Polyfill for CustomEvent constructor. Required for IE9-11.
 * https://developer.mozilla.org/en-US/docs/Web/API/CustomEvent/CustomEvent#Polyfill
 */

(function () {
  if (typeof window.CustomEvent === "function") return false;
  function CustomEvent(event, params) {
    params = params || { bubbles: false, cancelable: false, detail: null };
    var evt = document.createEvent('CustomEvent');
    evt.initCustomEvent(event, params.bubbles, params.cancelable, params.detail);
    return evt;
  }
  window.CustomEvent = CustomEvent;
})();

(function (Drupal, $, drupalSettings) {
  function ucfirst(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  $.fn.extend({
    cookiesOverlay: function (serviceId) {
      return this.each(function () {
        // Define default classes.
        var classStr = 'cookies-fallback';
        var baseStr = classStr + '--' + serviceId;
        var classes = [classStr, baseStr];
        // Define basic elements.
        var $element = $(this);
        var $elementWrapper = $element.parent();
        if (!($element.parent().hasClass(baseStr + '--wrap'))) {
          // Create and get the wrapper element. Caution, wrap returns the original set of elements:
          $elementWrapper = $element.wrapAll("<div />").parent().addClass(baseStr + '--wrap');
        }
        if ($element.data('status') !== 'fallback') {
          $element.data('status', 'fallback').addClass(classStr + '--element');
          var $fallback = $('<div />').addClass(classes.join(' '));
          $fallback.addClass(baseStr + '--overlay')
          // Create text box.
          var $textbox = $('<div />').addClass('cookies-fallback--text');
          $textbox.text(drupalSettings.cookies.services[serviceId].placeholderMainText);

          // Create Button to only accept current service (e.g. video only)
          var $button = $('<button />').addClass(baseStr + '--btn ' + classStr + '--btn');
          $button.text(drupalSettings.cookies.services[serviceId].placeholderAcceptText);

          // Add link behaviors: Dispatch Event, Remove class disabled from wrapper, set element status active.
          $button.on('click', function (event) {
            event.preventDefault();
            var services = {};
            services[serviceId] = true;
            document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { services } }));
          });

          // Create link to accept all cookies for all services
          var $acceptLink = $('<a href="#cookiesjsrAccept"/>').addClass(classStr + '--link');
          $acceptLink.text(drupalSettings.cookies.cookiesTexts.placeholderAcceptAllText);
          // Add link behaviors: Dispatch Event, Remove class disabled from wrapper, set element status active.
          $acceptLink.on('click', function (event) {
            event.preventDefault();
            document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { all: true } }));
          });

          // Remove fallback overlay when service becomes enabled.
          document.addEventListener('cookiesjsrUserConsent', function (event) {
            var service = (typeof event.detail.services === 'object') ? event.detail.services : {};
            if (typeof service[serviceId] !== 'undefined' && service[serviceId]) {
              var $wrapper = $element.parent('.' + baseStr + '--wrap');
              $element.data('status', 'active');
              // remove wrapper and overlay:
              $wrapper.after($element);
              $wrapper.remove();
            }
          })

          // Build overlay.
          $fallback.append($textbox);
          $fallback.append($button);
          $fallback.append($acceptLink);
          // Put overlay to page.
          $elementWrapper.append($fallback);
        }
        if (!$elementWrapper.is('.disabled')) {
          var wrapperClasses = classes.map(function (c) { return c + '--wrap' });
          wrapperClasses.push('disabled');
          $elementWrapper.addClass(wrapperClasses.join(' '));
        }
      });
    },
    // @todo This is currently only used for "cookies_filter". As a
    // cookies_filter does not have custom "placeholderMainText" nor a
    // custom "placeholderAcceptText", this should be resolved in the future!
    cookiesLegacyOverlay: function (serviceName) {
      return this.each(function () {
        // Define default classes.
        var classStr = 'cookies-fallback';
        var baseStr = classStr + '--' + serviceName;
        var classes = [classStr, baseStr];
        // Define basic elements.
        var $element = $(this);
        var $elementWrapper = $element.parent();
        if (!($element.parent().hasClass(baseStr + '--wrap'))) {
          // Create and get the wrapper element. Caution, wrap returns the original set of elements:
          $elementWrapper = $element.wrapAll("<div />").parent().addClass(baseStr + '--wrap');
        }
        if ($element.data('status') !== 'fallback') {
          $element.data('status', 'fallback').addClass(classStr + '--element');
          var $fallback = $('<div />').addClass(classes.join(' '));
          $fallback.addClass(baseStr + '--overlay')
          // Create text box.
          var $textbox = $('<div />').addClass('cookies-fallback--text');
          $textbox.text(Drupal.t('This content is blocked because @service cookies have not been accepted.',
            { '@service': ucfirst(serviceName) }));
          // Create Button.
          var $button = $('<button />').addClass(baseStr + '--btn ' + classStr + '--btn');
          $button.text(drupalSettings.cookies.cookiesTexts.placeholderAcceptAllText);

          // Add link behaviors: Dispatch Event, Remove class disabled from wrapper, set element status active.
          $button.on('click', function (event) {
            event.preventDefault();
            document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { all: true } }));
          });

          // Create link.
          var $acceptLink = $('<a href="#cookiesjsrAccept"/>').addClass(classStr + '--link');
          $acceptLink.text(Drupal.t('Only accept @service cookies', { '@service': ucfirst(serviceName) }));
          // Add link behaviors: Dispatch Event, Remove class disabled from wrapper, set element status active.
          $acceptLink.on('click', function (event) {
            event.preventDefault();
            var services = {};
            services[serviceName] = true;
            document.dispatchEvent(new CustomEvent('cookiesjsrSetService', { detail: { services } }));
          });

          // Remove fallback overlay when service becomes enabled.
          document.addEventListener('cookiesjsrUserConsent', function (event) {
            var service = (typeof event.detail.services === 'object') ? event.detail.services : {};
            if (typeof service[serviceName] !== 'undefined' && service[serviceName]) {
              var $wrapper = $element.parent('.' + baseStr + '--wrap');
              $element.data('status', 'active');
              // remove wrapper and overlay:
              $wrapper.after($element);
              $wrapper.remove();
            }
          })

          // Build overlay.
          $fallback.append($textbox);
          $fallback.append($button);
          $fallback.append($acceptLink);
          // Put overlay to page.
          $elementWrapper.append($fallback);
        }
        if (!$elementWrapper.is('.disabled')) {
          var wrapperClasses = classes.map(function (c) { return c + '--wrap' });
          wrapperClasses.push('disabled');
          $elementWrapper.addClass(wrapperClasses.join(' '));
        }
      });
    }
  });
}

)(Drupal, jQuery, drupalSettings);
