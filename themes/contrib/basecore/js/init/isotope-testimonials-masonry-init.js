(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtIsotopeTestimonialsMasonry = {
    attach: function (context, settings) {

      //Testimonials masonry
      once('mtIsotopeTestimonialsMasonryInit', ".testimonials-masonry-container", context).forEach(function(item) {
        var $this = $(item);
        $this.fadeIn("slow");

        // load images first
        $this.imagesLoaded(function() {
          var blockId = $this.closest(".block").attr('id'),
          masonryItem = "#" + blockId + " .masonry-item";
          $this.isotope({
            itemSelector: masonryItem,
            layoutMode: "masonry"
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
