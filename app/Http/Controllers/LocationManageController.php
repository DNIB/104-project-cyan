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
     * 引導至建立新地點的頁面
     * 
     * @return view
     */
    public function createUserLocation()
    {
        return view('map.addLocation', ['api' => $this->google_api]);
    }

    /**
     * 回傳使用者的地點及要求動作，至指定的視圖
     * 
     * @param string action = read
     * 
     * @return view
     */
    public function readUserLocation()
    {
        $VIEW = 'map.viewLocation';

        $user = Auth::user();
        $locations = $user->locations();

        $ret = [
            'locations' => $locations,
            'api' => $this->google_api,
            'api_token' => $user->api_token,
        ];

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
        $user_id = Auth::id();
        
        $name = $request->select_name;
        $desc = $request->select_desc;
        $lat = $request->lat_submit;
        $lng = $request->lng_submit;

        $isStringInvalid = empty($name);
        $isNumInvalid = !(is_numeric($lat) && is_numeric($lng));

        $isInputInvalid = $isStringInvalid || $isNumInvalid;

        if ($isInputInvalid) {
            abort(400);
        }

        $location = new Locations;

        $location->name = $name;
        $location->description = $desc;
        $location->lat = $lat;
        $location->lng = $lng;
        $location->appendLocation($user_id);

        return redirect()->back();
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

        $isStringInvalid = empty($name);
        $isLocationInvalid = !isset($location);

        $isInputInvalid = $isStringInvalid || $isLocationInvalid;

        if ( $isInputInvalid ) {
            abort(400);
        }

        Locations::where('id', $id)->update(
            [
            'name' => $name,
            'description' => $desc,
            ]
        );
        return redirect()->back();
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
        Locations::destroy($target_id);
        return redirect()->back();
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
