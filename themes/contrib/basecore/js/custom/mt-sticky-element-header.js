(function ($, Drupal) {
  Drupal.behaviors.mtStickyElementHeader = {
    attach: function (context, settings) {

      //The admin overlay menu height
      var adminHeight = parseInt($('body').css('paddingTop'));

      var topValue = adminHeight || 0;

      $(".mt-sticky-element-header").each(function(index, item) {
        $(this).css("top", (topValue)+"px");
      })

    }
  };
})(jQuery, Drupal);