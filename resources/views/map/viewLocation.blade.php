<!DOCTYPE html>
<html>
  <head>
    <meta name="csrf" id="csrf" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta id="trip_id" name="trip_id" content="{{ $trip_id }}"> 
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

    </style>
    <script>
        let map;
        let position = null;
        let unique_marker = null;
        let locations_data = null;
        let markers = {};

        // Get Data from Locations
        function initLocation() { 
            var trip_id = document.getElementById('trip_id').content;
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
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 24.978797790777875, lng: 121.5500428733606 },
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
            id = location['id'];

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
            for ( var key in markers) {
                element = markers[ key ];
                element.addListener("click", () => {
                    infoWindow.close();
                    infoWindow.setContent( element.getTitle() );
                    infoWindow.open(element.getMap(), element);
                });
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
         * 從右側欄位選取地點，並移動到地點之座標
         */
        function selectAction( target_id )
        {
            new_center = markers[ target_id ].position;
            map.panTo( new_center );
        }

        /**
         * 修改對應位置
         */
        function updateAction( target_id )
        {
            console.log( "Update" );
            console.log( target_id );

            document.getElementById("mapInfo").style.display = "none";
            document.getElementById("editWindows").style.display = "initial";

            //window.location = "/";
        }

        /**
         * 刪除對應位置
         */
        function deleteAction( target_id )
        {
            csfr = document.getElementById('csrf').content;
            URL = "/location/"+target_id;

            fetch(URL, {
                headers: {
                    'X-CSRF-TOKEN': csfr
                },
                method: 'DELETE',

            }).then( response => {
                return response.json();

            }).then( ret => {
                if ( ret.result == 'Success') {
                    alert("刪除資料成功");
                } else {
                    alert("刪除資料失敗：資料不存在");
                }
                window.location = "/";

            }).catch( function(){
                alert("發出請求或解讀資料失敗");
            });
        }
    </script>
  </head>
  <body>
    <div id="map"></div>

    <div class="mapInfo" id="mapInfo" name="mapInfo">
        @if ( count( $locations ) )
            @foreach ( $locations as $location)
                <?php
                    $target_unit = ( $action == 'edit' ) ? 'map.unit.editLocationUnit' : 'map.unit.showLocationUnit';
                ?>
                @component ( $target_unit )
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
        <h1> 編輯 </h1>
    </div>

    <!-- Async script executes immediately and must be after any DOM elements used in callback. -->
    <script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBODGF_8AvOjpKPhy5DMPPe9CsajdlWWTc&callback=initLocation&libraries=places&v=weekly"
    async
    ></script>

  </body>