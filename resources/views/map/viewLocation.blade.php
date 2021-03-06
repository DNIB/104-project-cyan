<!DOCTYPE html>
<html>
  @if ( Auth::check() )
  <head>
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" 
        rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" 
        crossorigin="anonymous">
    <meta name="csrf" id="csrf" content="{{ csrf_token() }}">
    <meta name="user_id" id="user_id" content="{{ Auth::id() }}">
    <meta charset="utf-8">
    <title>Simple Map</title>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <style type="text/css">
    /* Always set the map height explicitly to define the size of the div
    * element that contains the map. */
    #map {
        height: 100%;
        width: 70%;
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
        width: 30%;
    }

    .text-submit {
        height: 80px;
    }

    .button-submit {
        height: 40px;
    }

    .hide {
        display: none;
    }

    .display-table {
        display: flex;
        width: 100%;
        height: 50px;
        border: 1px solid;
        cursor: pointer;
    }

    .select-button {
        width: 15%;
        height: 40px;
        margin-right: 5%;
        margin-top: 5px;
    }

    .select-text {
        width: 65%;
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

      .back-index {
        position: absolute;
        left: 80%;
        top: 90%;
        height: 10%;
        width: 20%;
      }

      .back-button {
        height: 80%;
        width: 80%;
      }

    </style>
    <script>
        let map;
        let position = null;
        let unique_marker = null;
        let locations_data = null;
        let markers = {};

        // Get Data from Locations
        function initLocation() { 
            var user_id = document.getElementById('user_id').content;
            const api_url = '/api/user/getLocation' + '?api_token={{ $api_token }}';

            fetch(api_url, {
                method: 'GET'
            })
            .then( res => {
                return res.json();
            }).then( result => {
                locations_data = result;
                if ( locations_data.length > 0 ) {
                    initMap();
                }
            })/*.catch( function (){
                console.log( 'Get Data from API Failed' );
                console.log( 'url: ' + api_url );
            });*/
        }

        // Init Map
        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: locations_data[0].lat, lng: locations_data[0].lng },
                zoom: 12,
            });

            initMarker();
        }

        // Init Markers on Map
        function initMarker() {
            infoWindow = new google.maps.InfoWindow();
            locations_data_length = Object.keys(locations_data).length;

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
            id = location['id'];

            content = 
                "<h3>" + title + "</h3>" +
                "<h5> ?????????" + description + "</h5>";

            marker = new google.maps.Marker({
                position: position,
                title: content,
            });

            markers[ id ] = marker ;
        }

        // Set Markers on Map
        function setMarker() {
            for ( var key in markers) {
                element = markers[ key ];
                element.setMap(map);
            }
        }

        /**
         * Set Text Of LatLng Value
         */
        function setTextOfLatLng(lat_text, lng_text)
        {
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

        /**
         * ?????????????????????????????????????????????????????????
         */
        function selectAction( target_id )
        {
            target_marker = markers[ target_id ];
            
            infoWindow.close();
            infoWindow.setContent( target_marker.getTitle() );
            infoWindow.open(target_marker.getMap(), target_marker);

            new_center = target_marker.position;
            map.panTo( new_center );
        }

        /**
         * ??????????????????
         */
        function updateAction( target_id )
        {
            document.getElementById( "mapInfo" ).style.display = "none";
            document.getElementById( "editWindows" ).style.display = "initial";

            document.getElementById( "update_location_id").value = target_id;
            locations_data.forEach( location => {
                if ( target_id == location['id'] ) {
                    document.getElementById( "select_name" ).value = location[ "name" ];
                    document.getElementById( "select_desc" ).value = location[ "description" ];
                }
            });

            map.panTo( markers[ target_id ].position );
        }

        /**
         * ??????????????????
         */
        function cancelUpdateAction()
        {
            document.getElementById( "mapInfo" ).style.display = "initial";
            document.getElementById( "editWindows" ).style.display = "none";
        }
    </script>
  </head>
  <body>
    <div id="map"></div>

    <div class="mapInfo" id="mapInfo" name="mapInfo">
        @if ( count( $locations ) )
            @foreach ( $locations as $location)
                @component ( 'map.unit.editLocationUnit' )
                    @slot ( 'id' )
                        {{ $location['id'] }}
                    @endslot
                    @slot ( 'name' )
                        {{ $location['name'] }}
                    @endslot
                @endcomponent
            @endforeach
        @endif
    </div>

    <div class="editWindows hide" id="editWindows" name="editWindows">
        @component ( 'map.unit.infoLocationUnit' )
            @slot ( 'action' )
                {{ 'update' }}
            @endslot
        @endcomponent
    </div>

    @component ( 'map.unit.backIndex' )
    @endcomponent

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
    src="https://maps.googleapis.com/maps/api/js?key={{ $api }}&callback=initLocation&libraries=places&v=weekly"
    async
    ></script>

  </body>
  @else
    <h1> ?????????????????????????????????</h1>
  @endif
</html>