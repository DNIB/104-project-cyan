<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\Trips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class TripManageController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
    }

    public function viewTrip( $trip_id )
    {
        $ret = [];

        $trips = new Trips;
        $trip = $trips->find( $trip_id );

        $trip_locations_info = $trip->getAllLocationInfo();

        $ret[ 'locations' ] = $trip_locations_info;
        $ret[ 'trip_id'] = $trip_id;
        
        //return view('map.showLocation', ['trip_id' => $trip_id]);
        //return view( 'map.locationList', $ret );
        return view('view_trip', $ret);
    }
}
