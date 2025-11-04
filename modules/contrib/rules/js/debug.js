/**
 * @file
 * Adds the collapsible functionality to the rules debug log.
 */

(function ($, Drupal) {

  /**
   * Adds ability to "Open all" and "Close all" Rules debug log elements.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.rules_debug_log = {
    attach(context) {
      // Handle clicks on Open/Close text.
      $(once('rules-open-all-details', '.rules-debug-open-all', context)).click(

        function (event) {
          // Don't let the parent details element handle this event.
          event.preventDefault();
          // Don't let our other click handler, below, handle this event.
          event.stopPropagation();
          if ($(this).text() === Drupal.t('-Open all-')) {
            $(this).text(Drupal.t('-Close all-'));
            $('details').attr('open', '');
          } else {
            $(this).text(Drupal.t('-Open all-'));
            $('details').removeAttr('open');
          }
        },
      );

      // Toggle Open/Close text when the top level details element is opened or
      // closed via its native handler.
      $(once('rules-debug-log-details', '.rules-debug-log', context)).click(

        function (event) {
          if (typeof $(this).parent().attr('open') === 'undefined') {
            $('.rules-debug-open-all').text(Drupal.t('-Close all-'));
          } else {
            $('.rules-debug-open-all').text(Drupal.t('-Open all-'));
          }
        },
      );
    },
  };
})(jQuery, Drupal);
