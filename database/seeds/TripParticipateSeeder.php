<?php

use App\Models\TripParticipates;
use Illuminate\Database\Seeder;

class TripParticipateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run($max_trip, $max_player)
    {
        $TIMES = 5;
        $EXECUTE_TIME = $max_player * $TIMES;

        for( $index=0; $index<$EXECUTE_TIME; $index++ )
        {
            $trip_participate = new TripParticipates;

            $trip_participate->trip_id = random_int(1, $max_trip);
            $trip_participate->participate_id = random_int(1, $max_player);

            $trip_participate->save();
        }

        return;
    }
}
