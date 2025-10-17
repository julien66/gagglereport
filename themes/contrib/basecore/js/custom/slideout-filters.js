(function ($, Drupal, drupalSettings) {
  Drupal.behaviors.mtSlideoutFilters = {
    attach: function (context, settings) {
      $(window).click(function() {
        $(".view-filters--slideout").removeClass("view-filters--slideout-open");
        $(".main-content").removeClass("slideout-filters-open");
        $("body").removeClass("slideout-filters-open");
      });
      $(context).find('.slideout-filters-toggle').on('click', function(event) {
        event.stopPropagation();
        $(".view-filters--slideout").removeClass("view-filters--slideout-open").addClass("view-filters--slideout-open");
        $("body, .main-content").removeClass("slideout-filters-open").addClass("slideout-filters-open");
      });
      $(context).find('.slideout-filters-close-toggle').on('click', function(event) {
        event.stopPropagation();
        $(".view-filters--slideout").toggleClass("view-filters--slideout-open");
        $(".main-content").toggleClass("slideout-filters-open");
        $("body").toggleClass("slideout-filters-open");
      });
      $(context).find('.view-filters--slideout').on('click', function(event) {
        event.stopPropagation();
      });
    }
  };
})(jQuery, Drupal, drupalSettings);
