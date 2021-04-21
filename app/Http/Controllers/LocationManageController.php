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
    private $google_api = "AIzaSyAJhDKPXNDJXFhtDBL65Cow8MTmcDyY5Wc";

    /**
     * 讀入要求動作，並回傳對應的網頁
     * 
     * @param string $action
     * 
     * @return view
     */
    public function request( $action )
    {
        $this->checkLogin();

        switch ( $action ) {
        case "create":
            return view('map.addLocation', ['api' => $this->google_api]);

        case "read":
            return $this->readUserLocation( 'read' );

        case "edit":
            return $this->readUserLocation( 'edit' );

        default:
            break;
        }
        abort(404);
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
    public function readUserLocation( $action = 'read' )
    {
        $user_id = Auth::user()->id;
        $user = User::find( $user_id );

        $ret = [];

        $VIEW = 'map.viewLocation';

        $locations = $user->locations();
        $ret['action'] = $action;
        $ret['locations'] = $locations;
        $ret['api'] = $this->google_api;

        return view($VIEW, $ret);
    }

    /**
     * 依傳入的請求，於資料庫增加地點
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function createLocation( Request $request )
    {
        $this->checkLogin();

        $location = new Locations;

        $name = $request->select_name;
        $desc = $request->select_desc;
        $lat = $request->lat_submit;
        $lng = $request->lng_submit;

        $user_id = Auth::user()->id;

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
            return view('map.addLocation', ['api' => $this->google_api]);
        } else {
            abort(400);
        }
    }

    /**
     * 依傳入的請求，於資料庫更新地點
     * 
     * @param Request $request
     * 
     * @return view
     */
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
            return $this->readUserLocation( 'edit' );
        } else {
            abort(400);
        }
    }

    /**
     * 依傳入的請求，於資料庫刪除地點
     * 
     * @param Request $request
     * 
     * @return json
     */
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

    /**
     * 檢查登入狀況，若無登入則回傳 403
     */
    private function checkLogin()
    {
        $isNotLogin = !( Auth::check() );
        if ( $isNotLogin ) {
            abort(403);
        }
    }

    /**
     * 依傳入的使用者編號，回傳使用者的地點
     * 
     * @param integer $user_id
     * 
     * @return array
     */
    public function showUserLocation( $user_id )
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
