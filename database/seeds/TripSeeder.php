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
        $names = [
            'Holy Hand Grenade',
            'Operation Babarossa',
            'Action One',
        ];

        $max = count($names);

        for( $index=0; $index<$max; $index++ ) {
            $trip = new Trips;

            $trip->name = $names[$index];
            $trip->description = str_random(20);

            $trip->save();
        }

        return $max;
    }
}
