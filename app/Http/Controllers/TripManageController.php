<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\TripLocations;
use App\Models\TripParticipates;
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
            return $this->index();
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function updateLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $order_id = $request->order_id;
        $location_id = $request->location_id;

        $isTripIdValid = is_numeric( $trip_id );
        $isOrderIdValid = is_numeric( $order_id );
        $isLocationIdValid = is_numeric( $location_id );

        $isRequestValid = $isTripIdValid && $isOrderIdValid && $isLocationIdValid;

        if ( $isRequestValid ){
            $trip = Trips::find( $trip_id )->locations();

            $target = $trip->where( 'trip_order', $order_id )->get()[0];

            $target->location_id = $location_id;
            $target->save();
            return $this->index();
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function deleteLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $order_id = $request->order_id;

        $isTripIdValid = is_numeric( $trip_id );
        $isOrderIdValid = is_numeric( $order_id );

        $isRequestValid = $isTripIdValid && $isOrderIdValid;

        if ( $isRequestValid ){
            $trip = Trips::find( $trip_id )->locations();

            $target = $trip->where( 'trip_order', $order_id )->get()[0];
            $target->delete();

            return $this->index();
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function createTrip(Request $request)
    {   
        $user = Auth::user();
        
        $trip_name = $request->trip_name;
        $trip_desc = $request->trip_desc;

        $isNameValid = !empty( $trip_name );
        $isDescValid = !empty( $trip_desc );

        $isRequestValid = $isNameValid && $isDescValid;

        if ( $isRequestValid ) {
            $trip = new Trips;
            $trip->name = $trip_name;
            $trip->description = $trip_desc;
            $trip->save();

            $trip_participate = new TripParticipates;
            $trip_participate->trip_id = $trip->id;
            $trip_participate->participate_id = $user->player()->get()[0]->id;
            $trip_participate->save();

            return $this->index();
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function updateTrip(Request $request)
    {
        $trip_id = $request->trip_id;
        $trip_name = $request->trip_name;
        $trip_desc = $request->trip_desc;

        $isTripIdValid = is_numeric( $trip_id ) && ( !empty( Trips::find( $trip_id ) ) );
        $isNameValid = !empty( $trip_name );
        $isDescValid = !empty( $trip_desc );

        $isRequestValid = $isTripIdValid && $isNameValid && $isDescValid;

        if ( $isRequestValid ) {
            $trip = Trips::find( $trip_id );
            $trip->name = $trip_name;
            $trip->description = $trip_desc;
            $trip->save();

            return $this->index();
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    public function reorderLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $location_order = $request->location_order;
        $change = $request->change;

        $isTripIdValid = is_numeric( $trip_id ) && ( !empty( Trips::find( $trip_id ) ) );
        $isLocationOrderValid = is_numeric( $location_order );

        $isRequestValid = $isTripIdValid && $isLocationOrderValid;

        if ( $isRequestValid ) {
            $trip = Trips::find( $trip_id );
            $trip_location = $trip->locations();
            switch ( $change ) {
            case 'upper':
                $trip_exchange = $this->upperOrder( $trip_location, $location_order );
                break;
            case 'lower':
                $trip_exchange = $this->lowerOrder( $trip_location, $location_order );
                break;
            default:
                return view('welcome', ['status' => 'Request Invalid']);
                break;
            }
            $this->exchangeOrder( $trip_exchange );
            return $this->index();
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }

    private function upperOrder( $trip_location, $location_order )
    {
        return $trip_exchange = $trip_location->where('trip_order', '<=', $location_order)->orderBy('trip_order', 'desc')->limit(2)->get();
    }

    private function lowerOrder( $trip_location, $location_order )
    {
        return $trip_exchange = $trip_location->where('trip_order', '>=', $location_order)->orderBy('trip_order', 'asc')->limit(2)->get();
    }

    private function exchangeOrder( $trip_exchange )
    {
        $count = count( $trip_exchange );
        switch ( $count ) {
        case 1:
            break;
        case 2:
            $order_id_exchange = $trip_exchange[0]->trip_order;
            $trip_exchange[0]->trip_order = $trip_exchange[1]->trip_order;
            $trip_exchange[1]->trip_order = $order_id_exchange;
            $trip_exchange[0]->save();
            $trip_exchange[1]->save();
            break;
        default:
            break;
        }
    }

    public function deleteTrip(Request $request)
    {
        $trip_id = $request->trip_id;

        $isTripIdValid = is_numeric( $trip_id );

        if ( $isTripIdValid ) {
            $trip = Trips::find( $trip_id );
            $trip->delete();
            return $this->index();
        } else {
            return view('welcome', ['status' => 'Request Invalid']);
        }
    }
}
