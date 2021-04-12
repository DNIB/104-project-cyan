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
        width: 100%;
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

      /* Store Data of Location */
      .hide {
          display: none;
      }
    </style>
    <script>
      let map;
      let position = null;
      let markers = [];
      let locations_data = null;
      let infoWindow;

      // Get Data from Locations
      function initLocation() { 
        var trip_id = document.getElementById('trip_id').innerHTML;
        const api_url = '/api/trip/getLocation/' + trip_id;

        fetch(api_url, {method: 'GET'})
        .then( res => {
          return res.json();
        }).then( result => {
          locations_data = result;
          initMap();
        })/*.catch( function (){
          console.log( 'Get Data from API Failed' );
          console.log( 'url: ' + api_url );
        });*/
      }

      // Init Map
      function initMap() {
        first_location = locations_data[0];
        center = { lat: first_location['lat'], lng: first_location['lng']};

        map = new google.maps.Map(document.getElementById("map"), {
          center: center,
          zoom: 12,
        });
        
        initMarker();
      }

      // Init Markers on Map
      function initMarker() {
        infoWindow = new google.maps.InfoWindow();
        locations_data_length =  Object.keys(locations_data).length;
        
        for ( index=0; index<locations_data_length; index++ ) {
          addMarker( locations_data[ index ] );
        }

        setMarker();
      }

      // Add Marker to Markers
      function addMarker( location ) {
        position = { lat: location['lat'], lng: location['lng']};
        title = location['name'];
        description = location['description'];

        content = 
          "<h3>" + title + "</h3>" +
          "<h5> 描述：" + description + "</h5>";
        
        marker = new google.maps.Marker({
          position: position,
          title: content,
        });

        markers.push( marker );
      }

      // Set Markers on Map
      function setMarker() {
        markers.forEach( element => {
          element.addListener("click", () => {
            infoWindow.close();
            infoWindow.setContent( element.getTitle() );
            infoWindow.open(element.getMap(), element);
          });
          element.setMap(map);
        });
      }
    </script>
  </head>
  <body>
    <div id="map"></div>

    <b id="trip_id" name="trip_id" class="hide">{{ $trip_id }}</b>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBODGF_8AvOjpKPhy5DMPPe9CsajdlWWTc&callback=initLocation&libraries=&v=weekly"
    async
    ></script>
  </body>
</html>