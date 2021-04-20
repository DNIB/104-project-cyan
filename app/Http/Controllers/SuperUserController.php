<?php

namespace App\Http\Controllers;

use App\Models\Locations;
use App\Models\Trips;
use App\Models\Players;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SuperUserController extends Controller
{
    /**
     * 依據傳入的請求，更新使用者的資訊
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function update( Request $request )
    {
        $isUserInvalid = !$this->isSuperUser();
        if ( $isUserInvalid ) {
            return view('error.invalid_request');
        }

        $id = $request->id;
        $name = $request->name;
        $email = $request->email;
        $password = $request->password;

        $isPasswordEmpty = empty( $password );

        $isIdValid = !empty( User::find( $id ) );
        $isNameValid = !empty( $name );
        $isEmailValid = filter_var($email, FILTER_VALIDATE_EMAIL);
        $isPasswordValid = ( $isPasswordEmpty || (strlen( $password ) >= 8) );

        $isRequestValid = $isIdValid && $isNameValid && $isEmailValid && $isPasswordValid;

        if ( $isRequestValid ) {
            $user = User::find( $id );
            $user->name = $name;
            $user->email = $email;
            if ( !$isPasswordEmpty ) {
                $user->password = Hash::make( $password );
            }
            $user->save();
            return view('home');
        } else {
            return view('welcome', ['status' => '存在非法輸入']);
        }
    }

    /**
     * 依據傳入的請求，刪除使用者的資訊
     * 
     * @param Request $request
     * 
     * @return view
     */
    public function delete( Request $request )
    {
        $isUserInvalid = !$this->isSuperUser();
        if ( $isUserInvalid ) {
            return view('error.invalid_request');
        }

        $id = $request->delete_id;

        $isIdValid = is_numeric( $id );
        if ( $isIdValid ) {
            $user = User::find( $id );
            $isUserExist = !empty( $user );
            if ( $isUserExist ) {
                $user->delete();
                return view('home');
            } else {
                return view('welcome', ['status' => '無對應帳號']);
            }
        } else {
            return view('welcome', ['status' => '存在非法輸入']);
        }
    }

    /**
     * 取得所有行程的資料，並回傳至 view 以供顯示
     * 
     * @return view
     */
    public function showAllTrips()
    {
        $isUserInvalid = !$this->isSuperUser();
        if ( $isUserInvalid ) {
            return view('error.invalid_request');
        }

        $column = [
            'id',
            'name',
            'descrition',
            'created_at',
            'updated_at',
        ];
        $trips = Trips::all();

        $ret = [
            'columns' => $column,
            'rows' => $trips,
            'name' => "行程後台管理",
        ];

        return view('manage.main', $ret);
    }

    /**
     * 取得所有地點的資料，並回傳至 view 以供顯示
     * 
     * @return view
     */
    public function showAllLocations()
    {
        $isUserInvalid = !$this->isSuperUser();
        if ( $isUserInvalid ) {
            return view('error.invalid_request');
        }

        $column = [
            'id',
            'name',
            'description',
            'lat',
            'lng',
            'created_at',
            'updated_at',
        ];
        $locations = Locations::all();
        
        $ret = [
            'columns' => $column,
            'rows' => $locations,
            'name' => "地點後台管理",
        ];

        return view('manage.main', $ret);
    }

    /**
     * 取得所有參加者的資料，並回傳至 view 以供顯示
     * 
     * @return view
     */
    public function showAllPlayers()
    {
        $isUserInvalid = !$this->isSuperUser();
        if ( $isUserInvalid ) {
            return view('error.invalid_request');
        }

        $column = [
            'id',
            'name',
            'descriptipn',
            'user_id',
            'trip_id',
            'email',
            'phone',
        ];
        $players = Players::all();

        $ret = [
            'columns' => $column,
            'rows' => $players,
            'name' => "參加者後台管理",
        ];

        return view('manage.main', $ret);
    }

    /**
     * 確認當前使用者身份，是否有管理員資格
     * 
     * @return boolean
     */
    private function isSuperUser()
    {
        return ( Auth::check() ) ? ( Auth::user()->super_user ) : false;
    }
}
