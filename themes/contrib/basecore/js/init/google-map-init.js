(function ($, Drupal, drupalSettings, once) {
  Drupal.behaviors.mtGoogleMaps = {
    attach: function (context, settings) {

      function initialize() {
        once('mtGoogleMapsInit', ".google-map-canvas", context).forEach(function(item) {
          var map_locations_string = $(item).attr('data-attribute-mt-locations');
          var locations = JSON.parse(map_locations_string);
          var zoom = parseInt($(item).attr('data-attribute-mt-map-zoom'));
          var mapCenter = null;

          var mapOptions = {
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
          };

          var map = new google.maps.Map(item, mapOptions);
          var bounds = new google.maps.LatLngBounds();
          var infowindow = new google.maps.InfoWindow();
          var marker, i;

          for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
              position: new google.maps.LatLng(locations[i][1], locations[i][2]),
              map: map,
              draggable:false
            });
            bounds.extend(marker.position);
            google.maps.event.addListener(marker, 'click', (function(marker, i) {
              return function() {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
              }
            })(marker, i));
          }
          map.fitBounds(bounds);

          google.maps.event.addDomListener(window, "resize", function() {
            if (locations.length > 1) {
              map.fitBounds(bounds);
            } else {
              centerMarker = mapCenter || new google.maps.LatLng(locations[0][1], locations[0][2]);
              map.setCenter(centerMarker);
            }
          });

          if (zoom > 0) {
            var listener = google.maps.event.addListener(map, "idle", function () {
              map.setZoom(zoom);
              mapCenter = map.getCenter();
              google.maps.event.removeListener(listener);
            });
          }

          $(".field--mt-collapsible-block .google-map-canvas", context).closest('.collapse').on('shown.bs.collapse', function() {
            google.maps.event.trigger(map, 'resize');
            map.fitBounds(bounds);
          });

        });
      }
      google.maps.event.addDomListener(window, "load", initialize);

    }
  };
})(jQuery, Drupal, drupalSettings, once);
