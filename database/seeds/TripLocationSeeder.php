<?php

use App\Models\TripLocations;
use Illuminate\Database\Seeder;

class TripLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($max_location)
    {
        $TIMES = 1;
        $EXECUTE_TIME = $max_location * $TIMES;

        for( $index=0; $index<$EXECUTE_TIME; $index++ )
        {
            $trip_location = new TripLocations;

            $trip_location->trip_id = 1;
            $trip_location->location_id = random_int(1, $max_location);

            $trip_location->appendLocation($trip_location);
        }

        return;
    }
}
