(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtTypedjs = {
    attach: function (context, settings) {
      once('mtTypedjsInit', ".typed", context).forEach(function(item) {
        var typed = new Typed(item, {
          stringsElement: '.typed-strings',
          loop: true,
          typeSpeed: 30,
          backSpeed: 25,
          backDelay: 4000
        });
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
