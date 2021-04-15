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
}
