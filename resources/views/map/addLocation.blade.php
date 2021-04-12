<!DOCTYPE html>
<html>
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
          zoom: 16,
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

          // For each place, get the icon, name and location.
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
        console.log("Hello");
        isPlaceOnlyOne = (Object.keys(places).length == 1);
        if ( isPlaceOnlyOne ) {
          place = places[0];
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
        lat = document.getElementById('lat_value');
        lng = document.getElementById('lng_value');

        lat.innerHTML = lat_text;
        lng.innerHTML = lng_text;
      }

      /**
       * Connected to input button
       * Used to submit information
       */
      function selectPos()
      {
        isPositionSelected = ( position != null );

        if (isPositionSelected) {
          console.log( position.lat(), position.lng() );
        } else {
          console.log( "Marker doesn't exist" );
        }
        return;
      }
    </script>
  </head>
  <body>
    <div id="map"></div>
    
    <div class="mapInfo">
      <div class="inner">
        <label for="lat_value"> 緯度 </label>
        <h3 name="lat_value" id="lat_value"> (No Value) </h3>
        <label for="lng_value"> 經度 </label>
        <h3 name="lng_value" id="lng_value"> (No Value) </h3>
      </div>

      <label for="select_name" class="inner"> 地點名稱 </label>
      <input type="text" class="inner" id="select_name" name="select_name">

      <label for="select_des" class="inner"> 地點描述 </label>
      <textarea class="inner text-submit" id="select_des" name="select_des"></textarea>

      <input type="button" class="inner button-submit" id="select_pos" name="select_pos" value="選擇地點" onclick="selectPos()">

      <input type="text" class="search-text" id="search" name="search">
    </div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBODGF_8AvOjpKPhy5DMPPe9CsajdlWWTc&callback=initMap&libraries=places&v=weekly"
    async
    ></script>

  </body>