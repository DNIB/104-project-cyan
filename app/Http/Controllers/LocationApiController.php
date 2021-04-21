<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\User;
use App\Http\Resources\LocationResource;
use Illuminate\Http\Request;

class LocationApiController extends Controller
{
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
