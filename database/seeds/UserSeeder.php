<?php

use App\User;
use Illuminate\Database\Seeder;

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
        $user->password =  '$2y$10$mX/JKiT0nDrq9exJNUwJh.pcJEck5nCk8cUXzqPtxzV2stp2Y4Tx2';

        $user->save();
    }
}
