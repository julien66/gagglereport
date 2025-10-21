/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
(function (Drupal, $) {
  /**
   * Define defaults.
   */
  Drupal.behaviors.cookiesRecaptcha = {
    // id corresponding to the cookies_service.schema->id.
    id: 'recaptcha',

    consentGiven() {
      const scripts = document.querySelectorAll(`[id^="cookies_recaptcha_"]`);
      scripts.forEach((script) => {
        if (script && script.nodeName === 'SCRIPT') {
          const newScript = document.createElement('script');
          const attributes = Array.from(script.attributes);
          attributes.forEach((attr) => {
            const name = attr.nodeName;
            if (name !== 'type' && name !== 'id') {
              newScript.setAttribute(name, attr.nodeValue);
            }
          });
          newScript.innerHTML = script.innerHTML;
          script.parentNode.replaceChild(newScript, script);
        }
      });
    },

    consentDenied(context) {
      $('.g-recaptcha', context).cookiesOverlay('recaptcha');
    },

    attach(context) {
      const self = this;
      document.addEventListener('cookiesjsrUserConsent', function (event) {
        const service =
          typeof event.detail.services === 'object'
            ? event.detail.services
            : {};
        if (typeof service[self.id] !== 'undefined' && service[self.id]) {
          self.consentGiven(context);
        } else {
          self.consentDenied(context);
        }
      });
    },
  };
})(Drupal, jQuery);
