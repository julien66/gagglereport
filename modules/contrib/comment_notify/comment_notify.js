(function ($, Drupal) {
  "use strict";

  Drupal.behaviors.commentNotify = {
    attach: function (context) {
      $("#edit-notify, [id^='edit-notify--']", context)
        .bind("change", function () {
          $("#edit-notify-type, [id^='edit-notify-type--']", context)
            [this.checked ? "show" : "hide"]()
        })
        .trigger("change");
    },
  };
})(jQuery, Drupal);
