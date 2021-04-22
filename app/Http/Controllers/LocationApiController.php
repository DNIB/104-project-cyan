<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\User;
use App\Http\Resources\LocationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationApiController extends Controller
{
    /**
     * 依請求攜帶的使用者資訊，回傳使用者的地點
     * 
     * @return json
     */
    public function showUserLocation( Request $request )
    {   
        $user = Auth::user();
        $locations = $user->locations();

        return response()->json($locations);
    }

    /**
     * 依請求攜帶的使用者資訊，回傳使用者行程的地點
     * 
     * @return json
     */
    public function showTripLocation( Request $request, $trip_id )
    {   
        $user = Auth::user();
        $trip_info = $user->getTripInfo()[0];
        $locations = $trip_info[ 'locations' ];

        return response()->json($locations);
    }
    

    public function showLocation( Locations $location ): LocationResource
    {
        return new LocationResource ( $location );
    }
}
