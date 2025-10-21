/**
 * @file
 * Defines Javascript behaviors for the cookies module.
 */
((Drupal, $) => {
  /**
   * Define defaults.
   */
  $.fn.extend({
    cookiesHide(serviceName) {
      return this.each(function hide() {
        const $element = $(this);
        if (!$element.hasClass("hidden")) {
          $element.addClass("hidden");
        }
        // Show element again, if consent given:
        document.addEventListener(
          "cookiesjsrUserConsent",
          function consent(event) {
            const service =
              typeof event.detail.services === "object"
                ? event.detail.services
                : {};
            if (
              typeof service[serviceName] !== "undefined" &&
              service[serviceName]
            ) {
              $element.removeClass("hidden");
            }
          }
        );
      });
    },
  });

  Drupal.behaviors.cookiesFilter = {
    consentGiven(context, service) {
      $(
        `.cookies-filter-processed.cookies-filter-service--${service}`,
        context
      ).each(function heal(i, element) {
        const $element = $(element);
        // Heal 'iframe','embed', 'object', 'img', 'a'
        // Knock in CookiesConstants::COOKIES_SCRIPT_KO_TYPE -> 'text/javascript':
        if ($element.hasClass("cookies-filter-replaced--type")) {
          if ($element.attr("type") === "text/plain") {
            $element.removeAttr("type");
            $element.attr("type", "text/javascript");
            $element.removeClass("cookies-filter-replaced--type");
          }
        }
        // Knock in 'data-src' -> 'src':
        if ($element.hasClass("cookies-filter-replaced--src")) {
          if ($element.attr("src") !== $element.data("src")) {
            // We only allow http / https protocols to be used in the "src"
            // otherwise continue:
            const src = $element.data('src');
            const protocolRegex = /^(https?)/;
            if (src.substring(0, 1) !== '/') {
              const protocol = src.split(':')[0];
              if (!protocolRegex.test(protocol)) {
                // The src is not http nor https. Continue to the next element:
                return;
              }
            }
            $element.attr("src", $element.data("src"));
            $element.removeAttr("data-src");
            $element.removeClass("cookies-filter-replaced--src");
          }
        }
        // Knock in 'data-data' -> 'data':
        if ($element.hasClass("cookies-filter-replaced--data")) {
          if ($element.attr("data") !== $element.data("data")) {
            $element.attr("data", $element.data("data"));
            $element.removeAttr("data-data");
            $element.removeClass("cookies-filter-replaced--data");
          }
        }
        // Remove all other classes:
        $element.removeClass(
          `cookies-filter-processed cookies-filter-placeholder-type-hidden cookies-filter-placeholder-type-overlay cookies-filter-service--${service}`
        );
      });
      // Additionally look for custom selector elements and remove their classes
      // aswell:
      $(".cookies-filter-custom", context).removeClass(
        `cookies-filter-custom cookies-filter-placeholder-type-hidden cookies-filter-placeholder-type-overlay cookies-filter-service--${service}`
      );
    },

    consentDenied(context, service) {
      // Cookies overlay logic:
      $(
        `.cookies-filter-placeholder-type-overlay.cookies-filter-service--${service}`,
        context
      ).cookiesLegacyOverlay(service);
      // Cookies hidden logic:
      $(
        `.cookies-filter-placeholder-type-hidden.cookies-filter-service--${service}`,
        context
      ).cookiesHide(service);
    },
    attach(context) {
      const self = this;
      document.addEventListener(
        "cookiesjsrUserConsent",
        function handleConsent(event) {
          const services =
            typeof event.detail.services === "object"
              ? event.detail.services
              : {};
          // Loop through each services:
          Object.entries(services).forEach((service) => {
            if (typeof service[1] !== "undefined" && service[1]) {
              self.consentGiven(context, service[0]);
            } else {
              self.consentDenied(context, service[0]);
            }
          });
        }
      );
    },
  };
})(Drupal, jQuery);
