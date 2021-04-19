<?php

use App\Models\Trips;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return integer
     */
    public function run()
    {
        $trip = new Trips;

        $trip->name = 'Holy Hand Grenade';
        $trip->description = "SUPER HHG";

        $trip->save();
    }
}
