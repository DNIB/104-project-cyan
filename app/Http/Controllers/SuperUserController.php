<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
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
            return view('welcome', ['status' => '更新帳號成功']);
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
        $id = $request->delete_id;

        $isIdValid = is_numeric( $id );
        if ( $isIdValid ) {
            $user = User::find( $id );
            $isUserExist = !empty( $user );
            if ( $isUserExist ) {
                $user->delete();
                return view('welcome', ['status' => '刪除帳號成功']);
            } else {
                return view('welcome', ['status' => '無對應帳號']);
            }
        } else {
            return view('welcome', ['status' => '存在非法輸入']);
        }
    }
}
