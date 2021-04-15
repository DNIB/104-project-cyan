<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\Trips;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class TripManageController extends Controller
{
    public function index()
    {
        $isLogin = Auth::check();
        if ( $isLogin ) {
            $user = Auth::user();
            $trips = $user->getTripInfo();
            $ret = ['trips' => $trips ];
            return view( 'trip.index', $ret);
        } else {
            return view( 'error.invalid_request' );
        }
    }
}
