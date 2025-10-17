(function ($, Drupal) {
  Drupal.behaviors.mtStickyElement = {
    attach: function (context, settings) {

      //The admin overlay menu height
      var adminHeight = parseInt($('body').css('paddingTop'));

      // The Fixed header height
      var navigationHeight = $('.fixed-header-enabled .header').outerHeight(true);

      var topValue = adminHeight + navigationHeight + 10;

      $(".mt-sticky-element").each(function(index, item) {
        $(this).css("top", (topValue)+"px");
      })

    }
  };
})(jQuery, Drupal);