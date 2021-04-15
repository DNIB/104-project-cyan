<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\TripLocations;
use App\Models\Trips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class TripManageController extends Controller
{
    public function index()
    {
        $isLogin = Auth::check();
        if ( $isLogin ) {
            $user = Auth::user();
            $trips = $user->getTripInfo();
            $locations = $user->locations( true );
            $ret = [
                'trips' => $trips,
                'locations' => $locations,
            ];
            return view( 'trip.index', $ret);
        } else {
            return view( 'error.invalid_request' );
        }
    }

    public function createLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $location_id = $request->location_id;

        $isTripIdValid = is_numeric( $trip_id );
        $isLocationIdValid = is_numeric( $location_id );

        $isRequestValid = $isTripIdValid && $isLocationIdValid;

        if ( $isRequestValid ){
            $trip_location = new TripLocations();
            $trip_location->trip_id = $trip_id;
            $trip_location->location_id = $location_id;

            $trip_location->appendLocation();
            return view('welcome', ['status' => 'Request Valid']);
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function updateLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $order_id = $request->order_id-1;
        $location_id = $request->location_id;

        $isTripIdValid = is_numeric( $trip_id );
        $isOrderIdValid = is_numeric( $order_id );
        $isLocationIdValid = is_numeric( $location_id );

        $isRequestValid = $isTripIdValid && $isOrderIdValid && $isLocationIdValid;

        if ( $isRequestValid ){
            $trip = Trips::find( $trip_id )->locations();
            $target = $trip->where('trip_order', $order_id)->get()[0];
            $target->location_id = $location_id;
            $target->save();
            return view('welcome', ['status' => 'Request Valid']);
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function deleteLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $order_id = $request->order_id-1;

        $isTripIdValid = is_numeric( $trip_id );
        $isOrderIdValid = is_numeric( $order_id );

        $isRequestValid = $isTripIdValid && $isOrderIdValid;

        if ( $isRequestValid ){
            $trip = Trips::find( $trip_id )->locations();
            $targets = $trip->where('trip_order', '>=', $order_id)->get();

            $isDelete = true;
            foreach ( $targets as $target ) {
                if ( $isDelete ) {
                    $target->delete();
                    $isDelete = false;
                } else {
                    $target->trip_order -= 1;
                    $target->save();
                }
            }

            return view('welcome', ['status' => 'Request Valid']);
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function deleteTrip(Request $request)
    {
        $trip_id = $request->trip_id;

        $isTripIdValid = is_numeric( $trip_id );

        if ( $isTripIdValid ) {
            $trip = Trips::find( $trip_id );
            $trip->delete();
            return view('welcome', ['status' => 'Request Valid']);
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }
}
