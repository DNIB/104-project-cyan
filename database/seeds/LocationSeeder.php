<?php

use App\Models\Locations;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return integer
     */
    public function run()
    {
        $locations = [
            'First',
            'Second',
            'Third',
            'Fourth',
            'Fifth',
            'Sixth',
        ];

        $max = count($locations);

        for( $index=0; $index<$max; $index++ ) {
            $location = new Locations();

            $location->name = $locations[$index];
            $location->description = str_random(10);
            $location->coordinateN = random_int(121182952, 121902783) / 1000000;
            $location->coordinateE = random_int(24907016, 25285858) / 1000000;

            $location->save();
        }

        return $max;
    }
}
