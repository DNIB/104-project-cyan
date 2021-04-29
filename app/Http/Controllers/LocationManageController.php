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
        
        $datas = $this->getData(
            $request, 
            [
                'name',
                'description',
                'lat',
                'lng',
            ],
            [
                'select_name',
                'select_desc',
                'lat_submit',
                'lng_submit',
            ],
        );

        $isStringInvalid = empty($datas['name']);
        $isNumInvalid = !(is_numeric($datas['lat']) && is_numeric($datas['lng']));

        $isInputInvalid = $isStringInvalid || $isNumInvalid;

        if ($isInputInvalid) {
            abort(400);
        }

        $location = new Locations;
        $location->fill($datas);
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

        $datas = $this->getData(
            $request, 
            [
                'name',
                'description',
            ],
            [
                'select_name',
                'select_desc',
            ],
        );

        $isStringInvalid = empty($datas['name']);
        $isLocationInvalid = !isset($location);

        $isInputInvalid = $isStringInvalid || $isLocationInvalid;

        if ( $isInputInvalid ) {
            abort(400);
        }

        $location->update($datas);
        return redirect()->back();
    }

    /**
     * Get Data from Request by request_key
     * 
     * @param Request $request
     * @param array $database_keys
     * @param array $request_keys
     * 
     * @return array
     */
    private function getData( Request $request, $database_keys = [], $request_keys = [] )
    {
        $ret = [];
        foreach ($database_keys as $index => $key) {
            $request_key = $request_keys[$index];
            $ret[$key] = $request->$request_key;
        }
        return $ret;
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
