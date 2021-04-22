<!DOCTYPE html>
<html>
  <head>
    <title>Waypoints in Directions</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style type="text/css">
      #right-panel {
        font-family: "Roboto", "sans-serif";
        line-height: 30px;
        padding-left: 10px;
      }

      #right-panel select,
      #right-panel input {
        font-size: 15px;
      }

      #right-panel select {
        width: 100%;
      }

      #right-panel i {
        font-size: 12px;
      }

      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
      }

      #map {
        height: 100%;
        float: left;
        width: 100%;
        height: 100%;
      }

      #right-panel {
        margin: 20px;
        border-width: 2px;
        width: 20%;
        height: 400px;
        float: left;
        text-align: left;
        padding-top: 0;
      }

      #directions-panel {
        margin-top: 10px;
        background-color: #ffee77;
        padding: 10px;
        overflow: scroll;
        height: 174px;
      }
    </style>
    <script>
    let map;
    let position = null;
    let unique_marker = null;
    let locations_data = null;
    let markers = {};
    let id_increase = 0;

    // Get Data from Locations
    function initLocation() { 
        const api_url = '/api/user/getTripLocation/{{ $trip_id }}' + '?api_token={{ $api_token }}';

        fetch(api_url, {
            method: 'GET'
        })
        .then( res => {
            return res.json();

        }).then( result => {
            locations_data = result;

            if ( locations_data.length > 0 ) {
                initMap();
                initDirection();
            }
        })
    }

    // Init Map
    function initMap() {
        var first_location = locations_data[ 0 ][ 'location' ];
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: first_location.lat, lng: first_location.lng },
            zoom: 10,
        });

        initMarker();
    }

    // Init Markers on Map
    function initMarker() {
        infoWindow = new google.maps.InfoWindow();

        for ( var index in locations_data ) {
            addMarker( locations_data[ index ][ 'location' ] );
        }

        setMarker();
    }

    // Add Marker to Markers
    function addMarker( location ) {
        position = { lat: location['lat'], lng: location['lng']};
        title = location['name'];
        description = location['description'];
        id = id_increase;

        id_increase += 1;

        content = 
            "<h3>" + title + "</h3>" +
            "<h5> 描述：" + description + "</h5>";

        marker = new google.maps.Marker({
            position: position,
            title: content,
        });

        markers[ id ] = marker ;
    }

    // Set Markers on Map
    function setMarker() {
        for ( var key in markers ) {
            element = markers[ key ];
            element.setMap(map);
        }
    }

    // Initial Direction
    function initDirection(){
        const directionsService = new google.maps.DirectionsService();
        const directionsRenderer = new google.maps.DirectionsRenderer();
        directionsRenderer.setMap(map);

        calculateAndDisplayRoute(directionsService, directionsRenderer);
    }

    function calculateAndDisplayRoute(directionsService, directionsRenderer) {
        const waypts = [];
        const checkboxArray = document.getElementById("waypoints");

        var first = null;
        var last;

        for (let index in markers) {
            if ( first == null ) {
                first = index;
            }
            last = index;
            var mark = markers[ index ];
            waypts.push({
                location: mark.position,
                stopover: true
            });
        }

        console.log( waypts );
        console.log( first );
        console.log( last );

        directionsService.route(
        {
            origin: markers[ first ].position,
            destination: markers[ last ].position,
            waypoints: waypts,
            optimizeWaypoints: true,
            travelMode: google.maps.TravelMode.WALKING,
        },
        (response, status) => {
            if (status === "OK" && response) {
                directionsRenderer.setDirections(response);
                const route = response.routes[0];
                const summaryPanel = document.getElementById("directions-panel");
                summaryPanel.innerHTML = "";

                // For each route, display summary information.
                for (let i = 0; i < route.legs.length; i++) {
                    const routeSegment = i + 1;
                    summaryPanel.innerHTML +=
                    "<b>Route Segment: " + routeSegment + "</b><br>";
                    summaryPanel.innerHTML += route.legs[i].start_address + " to ";
                    summaryPanel.innerHTML += route.legs[i].end_address + "<br>";
                    summaryPanel.innerHTML +=
                    route.legs[i].distance.text + "<br><br>";
                }
            } else {
                window.alert("Directions request failed due to " + status);
            }
        }
        );
    }
    </script>
  </head>
  <body>
    <div id="map"></div>
    <div id="right-panel" style="display: hidden;">
      <div id="directions-panel"></div>
    </div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
      src="https://maps.googleapis.com/maps/api/js?key={{ $api }}&callback=initLocation&libraries=&v=weekly"
      async
    ></script>
  </body>
</html>