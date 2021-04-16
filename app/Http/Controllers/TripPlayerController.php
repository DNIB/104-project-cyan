<?php

namespace App\Http\Controllers;

use App\Models\TripParticipates;
use Illuminate\Http\Request;

class TripPlayerController extends Controller
{
    /**
     * 按傳入的行程編號，回傳行程的參加者的資料，以用來顯示網頁
     * 若登入者無訪問此行程的權限，則回傳錯誤頁面
     * 
     * @param integer $trip_id = 1
     * 
     * @return view
     */
    public function index( $trip_id = -1 )
    {
        $trip_players = TripParticipates::where('trip_id', $trip_id)->get();
        dd( $trip_players );
    }
}
