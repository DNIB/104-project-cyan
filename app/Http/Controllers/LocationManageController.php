<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\Trips;
use App\Http\Resources\LocationResource;
use App\Models\LocationEditor;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
    public function request( Request $request ,$action )
    {
        switch ( $action ) {
        case "create":
            return view('map.addLocation');
            break;
        case "read":
            return $this->readUserLocation( $request, 'read' );
            break;
        case "edit":
            return $this->readUserLocation( $request, 'edit' );
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

    /**
     * 回傳使用者的地點
     * 若無對應行程，則傳 -1 至 view
     * 
     * @param string action = read
     * @param integer $user_id
     * 
     * @return view
     */
    public function readUserLocation( Request $request ,$action = 'read' )
    {
        $user_id =  Auth::user()->id;
        $user = User::find( $user_id );

        $ret = [];
        $isActionValid = ( $action == 'read' ) || ( $action == 'edit' );
        if ( $isActionValid ) {
            $VIEW = 'map.viewLocation';
            $locations = $user->locations();
            $ret['action'] = $action;
            $ret['locations'] = $locations;
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

        $user_id = $request->user_id;

        $isStringValid = !( empty( $name ) || empty( $desc ) );
        $isNumValid = is_numeric( $lat ) && is_numeric( $lng );
        $isUserIdValud = is_numeric( $user_id );

        $isInputValid = $isStringValid && $isNumValid && $isUserIdValud;

        if ( $isInputValid ) {
            $location = new Locations;
            $location->name = $name;
            $location->description = $desc;
            $location->lat = $lat;
            $location->lng = $lng;
            $location->appendLocation( $user_id );
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
            LocationEditor::where('location_id', $target_id)->delete();
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

    public function showTripLocation( Request $request, $trip_id )
    {        
        $trip = new Trips;
        if ( $trip_id == 0) {
            $locations = new Locations;
            $locations = $locations->all();
        } else {
            $target_trip = $trip->find( $trip_id );
            $isTargetEmpty = $target_trip === null;
            
            if ( $isTargetEmpty ) {
                abort(404);
            } else {
                $locations = $target_trip->getAllLocationInfo();
            }
        }

        $ret = [];
        foreach ( $locations as $location) {
            $ret[] = $this->showLocation( $location );
        }
        return $ret;
    }

    public function showUserLocation( Request $request, $user_id )
    {   
        $isRequestLoginValid = true;

        if ( $isRequestLoginValid ) {
            $user = User::find( $user_id );
            
            $isUserValid = !empty( $user );
            if ( $isUserValid ){
                $locations = $user->locations();
                $isLocationValid = count($locations) > 0;
                if ( $isLocationValid ) {
                    $ret = $locations;
                } else {
                    $ret = ['status' => 'failed', 'result' => 'No Location Found'];
                }
            } else {
                $ret = ['status' => 'failed', 'result' => 'No User Found'];
            }
        } else {
            $ret = ['status' => 'failed', 'result' => 'API Request Refused'];
        }
        return $ret;
    }

    public function showLocation( Locations $location ): LocationResource
    {
        return new LocationResource ( $location );
    }
}
