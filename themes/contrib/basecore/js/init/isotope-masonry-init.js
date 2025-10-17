(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtIsotopeMasonry = {
    attach: function (context, settings) {

      //Masonry Layout Style 2
      once('mtIsotopeMasonryInit', ".masonry-container", context).forEach(function(item) {
        let $this = $(item);
        $this.fadeIn("slow");

        // load images first
        $this.imagesLoaded(function() {
          var blockId = $this.closest(".block").attr('id'),
          masonryItem = "#" + blockId + " .masonry-item";
          $this.isotope({
            itemSelector: masonryItem,
            layoutMode: "fitRows"
          });
          $this.isotope("layout");
        });

        // initialise inside bootstrap tab
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
          $this.isotope('layout');
        });
      });

    }
  };
})(jQuery, Drupal, drupalSettings, once);
