<?php

namespace App\Http\Controllers;

use App\Models\Locations;
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
            return view('map.addLocation');
            break;
        case "read":
            return $this->readLocation( 'read' );
            break;
        case "edit":
            return $this->readLocation( 'edit' );
            break;
        }
    }

    /**
     * 回傳行程內的地點，若參數為 0 則是回傳所有地點
     * 若無對應行程，則傳 -1 至 view
     * 
     * @param string action = read
     * @param integer $trip_id = 0
     * 
     * @return view
     */
    public function readLocation( $action = 'read', $trip_id = 0 )
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

        $ret = [];
        $isActionValid = ( $action == 'read' ) || ( $action == 'edit' );
        if ( $isActionValid ) {
            $VIEW = 'map.viewLocation';
            $locations = Locations::all()->toArray();
            $ret['action'] = $action;
            $ret['locations'] = $locations;
            $ret['trip_id'] = $trip_id;
            return view($VIEW, $ret);
        } else {
            abort(404);
        }
    }

    public function createLocation( Request $request )
    {
        $location = new Locations;

        $name = $request->select_name;
        $desc = $request->select_desc;
        $lat = $request->lat_submit;
        $lng = $request->lng_submit;

        $isStringValid = !( empty( $name ) || empty( $desc ) );
        $isNumValid = is_numeric( $lat ) && is_numeric( $lng );

        $isInputValid = $isStringValid && $isNumValid;

        if ( $isInputValid ) {
            $location = new Locations;
            $location->name = $name;
            $location->description = $desc;
            $location->coordinateN = $lat;
            $location->coordinateE = $lng;
            $location->save();
            return view('welcome', ['status' => '新增地點成功']);
        } else {
            return view('welcome', ['status' => '新增地點失敗']);
        }
    }

    public function updateLocation( Request $request )
    {
        $id = $request->location_id;
        $location = Locations::where('id', $id);

        $name = $request->select_name;
        $desc = $request->select_desc;

        $isStringValid = !( empty( $name ) || empty( $desc ) );
        $isLocationValid = ! empty( $location );

        $isInputValid = $isStringValid && $isLocationValid;

        if ( $isInputValid ) {
            Locations::where('id', $id)->update([
                'name' => $name,
                'description' => $desc,
            ]);
            return view('welcome', ['status' => '更新地點成功']);
        } else {
            return view('welcome', ['status' => '更新地點失敗']);
        }
    }

    public function deleteLocation( $target_id )
    {
        $location = Locations::find( $target_id );
        $isLocationValid = !empty( $location );

        if ( $isLocationValid ) {
            $location->delete();
            return response()->json([
                'result' => 'Success',
                'status' => 'Resource Delete',
            ]);
        } else {
            return response()->json([
                'result' => 'Fail',
                'status' => 'Resource Not Exist',
            ]);
        }
    }
}
