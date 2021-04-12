<?php

namespace App\Http\Controllers;

use App\Http\Resources\LocationResource;
use App\Models\Locations;
use App\Models\Trips;
use Illuminate\Http\Request;

class TripManageController extends Controller
{
    public function viewTrip( $trip_id )
    {
        $ret = [];

        $trips = new Trips;
        $trip = $trips->find( $trip_id );

        $trip_locations_info = $trip->getAllLocationInfo();

        $ret[ 'locations' ] = $trip_locations_info;
        
        return view('map.showLocation', ['trip_id' => $trip_id]);
        //return view( 'map.locationList', $ret );
    }

    public function showTripLocation( $trip_id )
    {
        $trip = new Trips;
        $target_trip = $trip->find( $trip_id );
        
        $locations = $target_trip->getAllLocationInfo();

        $ret = [];
        foreach ( $locations as $location) {
            $ret[] = $this->showLocation( $location );
        }
        return $ret;
    }

    public function showLocation( Locations $location ): LocationResource
    {
        return new LocationResource ( $location );
    }
}
