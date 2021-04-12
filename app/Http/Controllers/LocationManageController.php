<?php

namespace App\Http\Controllers;

use App\Models\Trips;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class LocationManageController extends Controller
{
    /**
     * 讀入要求動作，並回傳對應的網頁
     * 
     * @param string $action
     * 
     * @return view
     */
    public function request( $action )
    {
        switch ( $action ) {
        case "create":
            break;
        case "read":
            return $this->readLocation();
            break;
        case "update":
            break;
        case "delete":
            break;
        }
    }

    /**
     * 回傳行程內的地點，若參數為 0 則是回傳所有地點
     * 若無對應行程，則傳 -1 至 view
     * 
     * @param integer $trip_id
     * 
     * @return view
     */
    public function readLocation( $trip_id = 0 )
    {
        switch ( $trip_id ) {
        case 0:
            break;
        default:
            $trip = new Trips;
            $target = $trip->find( $trip_id );
            $isTripEmpty = isEmpty( $target );
            
            if ( $isTripEmpty ) {
                $trip_id = -1;
            }
            break;
        }

        return view('map.showLocation', ['trip_id' => $trip_id]);
    }
}
