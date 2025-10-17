jQuery(document).ready(function($) {
  var masonryContainer = $(".isotope-container"),
  filtersMasonry = $(".view-promoted-items .filters");
  filtersMasonry.prepend( "<li class=\"nav-item\"><a class=\"nav-link active mt-style-custom-all\" href=\"#\" data-filter=\"*\">" + drupalSettings.basecore.isotopeFiltersGridInit.isotopeFiltersText + "</a></li>" );

  $(".isotope-container").fadeIn("slow");

  masonryContainer.imagesLoaded(function() {
    masonryContainer.isotope({
      itemSelector: ".isotope-item",
      layoutMode : drupalSettings.basecore.isotopeFiltersGridInit.isotopeLayoutMode,
      transitionDuration: "0.6s",
      filter: "*"
    });
    filtersMasonry.find("a").click(function(){
      var $this = $(this);
      var selector = $this.attr("data-filter").replace(/\s+/g, "-");
      filtersMasonry.find("li a.active").removeClass("active");
      $this.addClass("active");
      masonryContainer.isotope({ filter: selector });
      return false;
    });
    masonryContainer.isotope("layout");
  });
});
