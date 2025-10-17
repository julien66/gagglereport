(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.inPageNavScrollspy = {
    attach: function (context, settings) {
      var target = $(".in-page-navigation").closest(".content").addClass("in-page-navigation-container");
      $('body').addClass("in-page-navigation-active");
      var toolbarHeight = parseInt($(context).find('body').css('paddingTop')) || 0;
      var scrollSpy = new bootstrap.ScrollSpy(document.body, {
        target: ".in-page-navigation-container",
        offset: drupalSettings.basecore.inPageNavigation.inPageNavigationOffset + toolbarHeight + 1
      });
    }
  };
})(jQuery, Drupal, drupalSettings, once);
