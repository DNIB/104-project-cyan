<?php

use App\Models\Players;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);

        // Construct Test User
        $user = new UserSeeder;
        $user->run();

        // This is what must be executed
        $arrival_method = new ArrivalMethodSeeder;
        $arrival_method->run();

        // Those under this is selected to executed
        // Used to produced test data
        $player = new PlayerSeeder;
        $trip = new TripSeeder;
        $location = new LocationSeeder;

        $player->run();
        $trip_count = $trip->run();
        $location_count = $location->run();

        $player_count = count(Players::all());

        $trip_participate = new TripParticipateSeeder;
        $trip_participate->run($trip_count, $player_count);

        $trip_location = new TripLocationSeeder;
        $trip_location->run($trip_count, $location_count);
    }
}
