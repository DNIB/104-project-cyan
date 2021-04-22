<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new User;

        $user->name = "Cyan";
        $user->email = "qazs0205@gmail.com";
        $user->password =  Hash::make( '12345678' );
        $user->api_token = Hash::make( Str::random(80) );
        $user->super_user = true;
        

        $user->save();

        $user = new User;

        $user->name = "cTest";
        $user->email = "ctest@gmail.com";
        $user->password =  Hash::make( '12345678' );
        $user->api_token = Hash::make( Str::random(80) );

        $user->save();
    }
}
