<?php

use App\Models\ArrivalMethods;
use Illuminate\Database\Seeder;

class ArrivalMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $names = [
            'walking',
        ];

        $max = count($names);

        for( $index=0; $index<$max; $index++ ) {
            $player = new ArrivalMethods;

            $player->name = $names[$index];

            $player->save();
        }

        return $max;
    }
}
