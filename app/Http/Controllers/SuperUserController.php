<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SuperUserController extends Controller
{
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
}
