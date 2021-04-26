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
    private $google_api = "AIzaSyBODGF_8AvOjpKPhy5DMPPe9CsajdlWWTc";

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
            return view('map.addLocation', ['api' => $this->google_api]);

        case "read":
            return $this->readUserLocation('read');

        case "edit":
            return $this->readUserLocation('edit');

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
     * 
     * @return view
     */
    public function readUserLocation( $action = 'read' )
    {
        $user = Auth::user();

        $ret = [];

        $VIEW = 'map.viewLocation';

        $locations = $user->locations();
        $ret['action'] = $action;
        $ret['locations'] = $locations;
        $ret['api'] = $this->google_api;
        $ret['api_token'] = $user->api_token;

        return view($VIEW, $ret);
    }

    /**
     * 依傳入的請求，於資料庫增加地點
     * 請求應有資料：
     *   - (string) select_name (must exist)
     *   - (string) select_desc
     *   - (float) lat_submit (must exist)
     *   - (float) lng_submit (must exist)
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function createLocation( Request $request )
    {
        $name = $request->select_name;
        $desc = $request->select_desc;
        $lat = $request->lat_submit;
        $lng = $request->lng_submit;

        $user_id = Auth::id();

        $isStringValid = !( empty($name) || empty($desc) );
        $isNumValid = is_numeric($lat) && is_numeric($lng);
        $isUserIdValud = is_numeric($user_id);

        $isInputValid = $isStringValid && $isNumValid && $isUserIdValud;

        if ($isInputValid ) {
            $location = new Locations;

            $location->name = $name;
            $location->description = $desc;
            $location->lat = $lat;
            $location->lng = $lng;

            $location->appendLocation($user_id);
            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依傳入的請求，於資料庫更新地點
     * 請求應有資料：
     *   - (integer) location_id (must exist)
     *   - (string) select_name (must exist)
     *   - (string) select_desc
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function updateLocation( Request $request )
    {
        $id = $request->location_id;
        $location = Locations::find($id);

        $name = $request->select_name;
        $desc = $request->select_desc;

        $isStringValid = !( empty($name) || empty($desc) );
        $isLocationValid = isset($location);

        $isInputValid = $isStringValid && $isLocationValid;

        if ($isInputValid ) {
            Locations::where('id', $id)->update(
                [
                'name' => $name,
                'description' => $desc,
                ]
            );
            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 依傳入的請求，於資料庫刪除地點
     * 請求應有資料：
     *   - (integer) location_id
     * 
     * @param Request $request
     * 
     * @return json
     */
    public function deleteLocation( Request $request )
    {
        $target_id = $request->location_id;
        $location = Locations::find($target_id);

        $isLocationValid = isset($location);

        if ($isLocationValid ) {
            LocationEditor::where('location_id', $target_id)->delete();
            $location->delete();

            return redirect()->back();
        }
        abort(400);
    }

    /**
     * 傳入行程編號，驗證使用者後回傳資料至 View
     * 
     * @param integer $trip_id
     * 
     * @return view
     */
    public function tripMap( $trip_id )
    {
        
        return view(
            'trip.map_display', [
            'trip_id' => $trip_id,
            'api' => $this->google_api,
            'api_token' => Auth::user()->api_token,
            ]
        );
    }
}
