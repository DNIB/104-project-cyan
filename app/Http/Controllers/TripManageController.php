<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\Players;
use App\Models\TripLocations;
use App\Models\TripParticipates;
use App\Models\Trips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class TripManageController extends Controller
{
    /**
     * 回傳管理行程的頁面
     * 會依 Auth 取得當前登入者的資訊，來顯示相對的資料
     * 
     * @return view
     */
    public function index()
    {
        $user = Auth::user();
        $trips = $user->getTripInfo();
        
        $locations = $user->locations( true );

        $ret = [
            'trips' => $trips,
            'locations' => $locations,
        ];
        
        return view( 'trip.index', $ret);
    }

    /**
     * 依傳入的請求，在資料庫裡新增地點
     * 請求需有資料：
     *   (integer) trip_id, 
     *   (integer) location_id
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function createLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $location_id = $request->location_id;

        $isTripIdValid = is_numeric( $trip_id ) && ( null !== Trips::find( $trip_id ) );
        $isLocationIdValid = is_numeric( $location_id ) && ( null !== Locations::find( $location_id ) );

        $isRequestValid = $isTripIdValid && $isLocationIdValid;

        if ( $isRequestValid ){
            $trip_location = new TripLocations();
            $trip_location->trip_id = $trip_id;
            $trip_location->location_id = $location_id;

            $trip_location->appendLocation();

            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依傳入的請求，在資料庫裡更新地點資訊
     * 請求需有資料：
     *   (integer) trip_id, 
     *   (integer) order_id
     *   (integer) location_id
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function updateLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $order_id = $request->order_id;
        $location_id = $request->location_id;

        $isTripIdValid = is_numeric( $trip_id ) && ( null !== Trips::find( $trip_id ) );
        $isOrderIdValid = is_numeric( $order_id );
        $isLocationIdValid = is_numeric( $location_id ) && ( null !== Locations::find( $location_id ) );

        $isRequestValid = $isTripIdValid && $isOrderIdValid && $isLocationIdValid;

        if ( $isRequestValid ){
            $trip = Trips::find( $trip_id )->locations();

            $target = $trip->where( 'trip_order', $order_id )->get()[0];

            $target->location_id = $location_id;
            $target->save();

            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依傳入的請求，在資料庫裡刪除指定順序的地點
     * 請求需有資料：
     *   (integer) trip_id, 
     *   (integer) order_id
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function deleteLocation(Request $request)
    {
        $trip_id = $request->trip_id;
        $order_id = $request->order_id;

        $isTripIdValid = is_numeric( $trip_id ) && ( null !== ( Trips::find( $trip_id ) ) );

        $target = TripLocations::where( 'trip_order', $order_id )->get();
        $isOrderIdValid = is_numeric( $order_id ) && ( count( $target ) > 0 );

        $isRequestValid = $isTripIdValid && $isOrderIdValid;

        if ( $isRequestValid ){
            $trip = Trips::find( $trip_id )->locations();

            $target = $trip->where( 'trip_order', $order_id )->get()[0];
            $target->delete();

            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依傳入的請求，在資料庫裡新增行程
     * 請求需有資料： 
     *   (string) trip_name
     *   (string) trip_desc
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function createTrip(Request $request)
    {   
        $user = Auth::user();
        
        $trip_name = $request->trip_name;
        $trip_desc = $request->trip_desc;

        $isNameValid = !empty( $trip_name );
        $isRequestValid = $isNameValid;

        if ( $isRequestValid ) {
            $trip = new Trips;
            $trip->name = $trip_name;
            $trip->description = $trip_desc;
            $trip->save();

            $player = new Players;
            $player->name = $user->name;
            $player->description = "(myself)";
            $player->user_id = $user->id;
            $player->email = $user->email;
            $player->trip_id = $trip->id;
            $player->trip_creator = true;
            $player->save();

            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依傳入的請求，在資料庫裡更新指定行程資訊
     * 請求需有資料： 
     *   (integer) trip_id
     *   (string) trip_name
     *   (string) trip_desc
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function updateTrip(Request $request)
    {
        $trip_id = $request->trip_id;
        $trip_name = $request->trip_name;
        $trip_desc = $request->trip_desc;

        $isTripIdValid = is_numeric( $trip_id ) && ( null !== Trips::find( $trip_id )  );
        $isNameValid = !empty( $trip_name );
        $isDescValid = !empty( $trip_desc );

        $isRequestValid = $isTripIdValid && $isNameValid && $isDescValid;

        if ( $isRequestValid ) {
            $trip = Trips::find( $trip_id );
            $trip->name = $trip_name;
            $trip->description = $trip_desc;
            $trip->save();

            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依傳入的請求，在資料庫裡移動地點的順序
     * 請求需有資料： 
     *   (integer) trip_id
     *   (integer)) location_order
     *   (string) change
     *     - 此值應為 'upper' 或是 'lower'
     * 
     * @param Request $request
     * 
     * @return view
     */
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
                abort(400);
            }
            $this->exchangeOrder( $trip_exchange );

            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 傳入行程的地點，以及需要處理的地點順序之編號
     * 回傳指定順序的地點，以及該地點之前一地點（若存在的話）
     * 
     * @param Locations $trip_location
     * @param integer $location_order
     * 
     * @return Locations $trip_location
     */
    private function upperOrder( $trip_location, $location_order )
    {
        return $trip_location->where('trip_order', '<=', $location_order)->orderBy('trip_order', 'desc')->limit(2)->get();
    }

    /**
     * 傳入行程的地點，以及需要處理的地點順序之編號
     * 回傳指定順序的地點，以及該地點之後一地點（若存在的話）
     * 
     * @param Locations $trip_location
     * @param integer $location_order
     * 
     * @return Locations $trip_location
     */
    private function lowerOrder( $trip_location, $location_order )
    {
        return $trip_location->where('trip_order', '>=', $location_order)->orderBy('trip_order', 'asc')->limit(2)->get();
    }

    /**
     * 傳入行程的地點，長度應為 <= 2，若長度剛好為 2，則將兩個地點的順序交換
     * 若長度不為 2，則不做任何處理
     * 
     * @param Locations $trip_exchange
     */
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

    /**
     * 依傳入的請求，在資料庫裡刪除指定的行程
     * 請求需有資料： 
     *   (integer) trip_id
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function deleteTrip(Request $request)
    {
        $trip_id = $request->trip_id;

        $target_trip = ( is_numeric( $trip_id ) ) ? Trips::find( $trip_id ) : null;

        $isTargetValid = isset( $target_trip );

        if ( $isTargetValid ) {
            $target_trip->delete();
            return redirect()->back();
        }
        abort(400);
    }
}
