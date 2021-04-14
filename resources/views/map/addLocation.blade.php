<!DOCTYPE html>
<html>
  @if ( Auth::check() )
  <head>
    <meta charset="utf-8">
    <title>Simple Map</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style type="text/css">
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 100%;
        width: 75%;
      }

      #pac-input {
        background-color: #fff;
        font-family: Roboto;
        font-size: 15px;
        font-weight: 300;
        margin-left: 12px;
        padding: 0 11px 0 13px;
        text-overflow: ellipsis;
        width: 400px;
      }

      /* Optional: Makes the sample page fill the window. */
      html,
      body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
        display: flex;
      }
      
      .mapInfo {
        width: 25%;
      }

      .inner {
        margin-top: 10px;
        margin-bottom: 10px;
        margin-left: 10%;
        width: 80%;
      }

      .text-submit {
        height: 80px;
      }

      .button-submit {
        height: 40px;
      }

      .search-text {
        width: 50%;
        height: 20px;
        margin-top: 20px;
        display: hide;
      }

      .hide {
        display: none;
      }
    </style>
    <script>
      let map;
      let position = null;
      let unique_marker = null;

      // Init Map
      function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
          center: { lat: 24.978797790777875, lng: 121.5500428733606 },
          zoom: 12,
        });

        // This event listener will call addMarker() when the map is clicked.
        map.addListener("click", (event) => {
          addMarker(event.latLng);
        });

        // Create the search box and link it to the UI element.
        const input = document.getElementById("search");
        const searchBox = new google.maps.places.SearchBox(input);
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Bias the SearchBox results towards current map's viewport.
        map.addListener("bounds_changed", () => {
          searchBox.setBounds(map.getBounds());
        });

        let markers = [];
        // Listen for the event fired when the user selects a prediction and retrieve
        // more details for that place.
        searchBox.addListener("places_changed", () => {
          const places = searchBox.getPlaces();

          if (places.length == 0) {
            return;
          }
          // Clear out the old markers.
          markers.forEach((marker) => {
            marker.setMap(null);
          });
          markers = [];

          // Show LatLng Infomation if Place only exited one place
          setLatLngValueByPlace(places);

          // For each place, get the name and location.
          const bounds = new google.maps.LatLngBounds();
          places.forEach((place) => {
            if (!place.geometry || !place.geometry.location) {
              console.log("Returned place contains no geometry");
              return;
            }
            // Create a marker for each place.
            markers.push(
              new google.maps.Marker({
                map,
                title: place.name,
                position: place.geometry.location,
              })
            );

            if (place.geometry.viewport) {
              // Only geocodes have viewport.
              bounds.union(place.geometry.viewport);
            } else {
              bounds.extend(place.geometry.location);
            }
          });
          map.fitBounds(bounds);
        });
      }

      // Adds a marker on the map
      function addMarker(location_select) {
        position = location_select;

        if ( unique_marker != null ) {
            unique_marker.setMap(null);
        }
        
        unique_marker = new google.maps.Marker({
            position: position,
            title: "目標地點",
        });

        setTextOfLatLng( position.lat(), position.lng() );

        unique_marker.setMap(map);
      }

      /**
       * Set LatLng Value on the Page By Place
       */
      function setLatLngValueByPlace(places)
      {
        isPlaceOnlyOne = (Object.keys(places).length == 1);

        if ( isPlaceOnlyOne ) {
          place = places[0];

          setInputName( place.name );

          location_select = place.geometry.location;
          setTextOfLatLng( location_select.lat(), location_select.lng() );

        } else {
          setTextOfLatLng( "(Too Many Places)", "(Too Many Places)" );
        }
      }

      /**
       * Set Text Of LatLng Value
       */
      function setTextOfLatLng(lat_text, lng_text)
      {
        lat_show = document.getElementById('lat_value');
        lng_show = document.getElementById('lng_value');

        lat_show.innerHTML = lat_text;
        lng_show.innerHTML = lng_text;

        lat_submit = document.getElementById('lat_submit');
        lng_submit = document.getElementById('lng_submit');

        lat_submit.value = lat_text;
        lng_submit.value = lng_text;
      }

      /**
       * Auto Fill Location Name of Input
       */
      function setInputName( target_name )
      {
        console.log("TRY");
        console.log(target_name);

        document.getElementById('select_name').value = target_name;
      }
    </script>
  </head>
  <body>
    <div id="map"></div>
    
    <div class="mapInfo">
      @component ( 'map.unit.infoLocationUnit' )
        @slot ( 'action' )
          {{ 'create' }}
        @endslot
        @slot ( 'method' )
          {{ 'POST' }}
        @endslot
      @endcomponent
      <input type="text" class="search-text" id="search" name="search">
    </div>
    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBODGF_8AvOjpKPhy5DMPPe9CsajdlWWTc&callback=initMap&libraries=places&v=weekly"
    async
    ></script>

  </body>
  @else
    <h1> 非法操作：請登入後嘗試</h1>
  @endif
</html>